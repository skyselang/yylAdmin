<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\setting\AccordCache as Cache;
use app\common\model\setting\AccordModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;

/**
 * 协议管理
 */
class AccordService
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
        'accord_id' => '',
        'unique/s'  => '',
        'name/s'    => '',
        'desc/s'    => '',
        'content/s' => '',
        'remark/s'  => '',
        'sort/d'    => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps = $exp ? where_exps() : [];

        return ['exps' => $exps];
    }

    /**
     * 协议列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="accord_id,unique,name,desc,remark,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',unique,name,desc,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $append = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }

        $count = $pages = 0;
        if ($total) {
            $count = model_where($model->clone(), $where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where);
        $list  = $model->append($append)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 协议信息
     * @param int|string $id   协议id、编号
     * @param bool       $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="accord_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('协议不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_disable_name'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 协议添加
     * @param array $param 协议信息
     * @Apidoc\Param(ref={Model::class}, withoutField="accord_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 协议修改
     * @param int|array $ids   协议id
     * @param array     $param 协议信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid'] = user_id();
        $param['update_time'] = datetime();

        $unique = $model->where($pk, 'in', $ids)->column('unique');
        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $param;
    }

    /**
     * 协议删除
     * @param array $ids  协议id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

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
        $cache->del($ids);
        $cache->del($unique);

        return $res;
    }

    /**
     * 协议是否禁用
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
     * 协议批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 协议导出导入表头
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
            ['field' => 'unique', 'name' => lang('编号'), 'width' => 22],
            ['field' => 'name', 'name' => lang('名称'), 'width' => 22, 'color' => 'FF0000'],
            ['field' => 'desc', 'name' => lang('描述'), 'width' => 30],
            ['field' => 'content', 'name' => lang('内容'), 'width' => 50],
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
     * 协议导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SETTING_ACCORD;

        $field = 'unique,name,desc,remark,sort,is_disable,create_time,update_time,content';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }
}
