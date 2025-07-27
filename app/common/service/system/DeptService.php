<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\system\DeptCache as Cache;
use app\common\model\system\DeptModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\model\system\UserAttributesModel;
use app\common\cache\system\UserCache;

/**
 * 部门管理
 */
class DeptService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 添加修改字段
     */
    public static $editField = [
        'dept_id'       => '',
        'dept_pid/d'    => 0,
        'dept_unique/s' => '',
        'dept_name/s'   => '',
        'abbr/s'        => '',
        'desc/s'        => '',
        'tel/s'         => '',
        'fax/s'         => '',
        'email/s'       => '',
        'addr/s'        => '',
        'remark/s'      => '',
        'sort/d'        => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'dept_pid', 'dept_unique', 'abbr'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("trees", ref={Model::class}, type="tree", desc="树形", field="dept_id,dept_pid,dept_name"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps  = $exp ? where_exps() : [];
        $trees = self::list('tree', [where_delete()], [], 'dept_name');

        return ['exps' => $exps, 'trees' => $trees];
    }

    /**
     * 部门列表
     * @param string $type  tree树形，list列表
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * @param int    $page  页数
     * @param int    $limit 数量
     * @return array []
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned("list", type="tree", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="dept_id,dept_pid,dept_unique,dept_name,abbr,desc,remark,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     * })
     */
    public static function list($type = 'tree', $where = [], $order = [], $field = '', $page = 0, $limit = 0, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'asc'];
        }
        if (empty($field)) {
            $field = $pk . ',' . $pidk . ',dept_unique,dept_name,abbr,desc,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $pidk . ',' . $field;
        }

        $cache = self::cache();
        $key   = where_cache_key($type, $where, $order, $field, $page, $limit, $param);
        $data  = $cache->get($key);
        if (empty($data)) {
            $append = [];
            if (strpos($field, 'is_disable')) {
                $append[] = 'is_disable_name';
            }
            if ($page > 0) {
                $model = $model->page($page);
            }
            if ($limit > 0) {
                $model = $model->limit($limit);
            }
            $model = $model->field($field);
            $model = model_where($model, $where);
            $data  = $model->append($append)->order($order)->select()->toArray();
            if ($type == 'tree') {
                $data = array_to_tree($data, $pk, $pidk);
            }
            $cache->set($key, $data);
        }

        return $data;
    }

    /**
     * 部门信息
     * @param int  $id   部门id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="dept_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('部门不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_disable_name'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 部门添加
     * @param array $param 部门信息
     * @Apidoc\Param(ref={Model::class}, withoutField="dept_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 部门修改 
     * @param int|array $ids   部门id
     * @param array     $param 部门信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->clear();

        return $param;
    }

    /**
     * 部门删除
     * @param array $ids  部门id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = update_softdele();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->clear();

        return $update;
    }

    /**
     * 部门是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        return $data;
    }

    /**
     * 部门批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'dept_unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 部门导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'dept_pid', 'name' => lang('上级ID'), 'width' => 12],
            ['field' => 'dept_unique', 'name' => lang('编号'), 'width' => 22],
            ['field' => 'dept_name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => 'abbr', 'name' => lang('简称'), 'width' => 12],
            ['field' => 'tel', 'name' => lang('电话'), 'width' => 16],
            ['field' => 'fax', 'name' => lang('传真'), 'width' => 16],
            ['field' => 'email', 'name' => lang('邮箱'), 'width' => 16],
            ['field' => 'addr', 'name' => lang('地址'), 'width' => 16],
            ['field' => 'desc', 'name' => lang('描述'), 'width' => 30],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22, 'type' => 'time'],
        ];
        // 生成下标
        foreach ($header as $index => &$value) {
            $value['index'] = $index;
        }
        if ($exp_imp == 'import') {
            $header[] = ['index' => -1, 'field' => 'result_msg', 'name' => lang('导入结果'), 'width' => 60];
        }

        return $header;
    }

    /**
     * 部门导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 1;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SYSTEM_DEPT;

        $field = 'dept_unique,dept_name,abbr,tel,fax,email,addr,desc,remark,sort,is_disable,create_time,update_time';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 部门用户列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref={Model::class}, field="dept_id")
     * @Apidoc\Query(ref={UserService::class,"list"})
     * @Apidoc\Returned(ref={UserService::class,"list"})
     */
    public static function userList($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return UserService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 部门用户解除
     * @param array $dept_id  部门id
     * @param array $user_ids 用户id
     * @Apidoc\Param("dept_id", type="array", require=true, desc="部门id")
     * @Apidoc\Param("user_ids", type="array", require=false, desc="用户id，为空则解除所有用户")
     */
    public static function userLift($dept_id, $user_ids = [])
    {
        $where[] = ['dept_id', 'in', $dept_id];
        if (empty($user_ids)) {
            $user_ids = UserAttributesModel::where($where)->column('user_id');
        }
        $where[] = ['user_id', 'in', $user_ids];

        $res = UserAttributesModel::where($where)->delete();

        $cache = new UserCache();
        $cache->del($user_ids);

        return $res;
    }
}
