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
use app\common\cache\system\MessageCache as Cache;
use app\common\model\system\MessageModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\model\system\UserMessageModel;

/**
 * 消息管理
 */
class MessageService
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
        'message_id' => '',
        'unique/s'   => '',
        'title/s'    => '',
        'content/s'  => '',
        'remark/s'   => '',
        'sort/d'     => 250,
        'user_ids/a' => [],
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique', 'user_ids'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("users", type="array", desc="用户", children={
     *     @Apidoc\Returned(ref={UserService::class,"info"}, field="user_id,nickname,username"),
     *   })   
     * })
     */
    public static function basedata($exp = false)
    {
        $exps  = $exp ? where_exps() : [];
        $users = UserService::list([], 0, 0, [], 'nickname,username', false)['list'] ?? [];

        return ['exps' => $exps, 'users' => $users];
    }

    /**
     * 消息列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="message_id,unique,title,remark,is_disable,sort,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
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
            $field = $pk . ',unique,title,remark,is_disable,sort,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $field = select_field($field, $field_no);

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
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 消息信息
     * @param int  $id   消息id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="message_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getUserIdsAttr"}, field="user_ids")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->with(['userMessage'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('消息不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_disable_name', 'user_ids'])->hidden(['userMessage'])->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 消息添加
     * @param array $param 消息信息
     * @Apidoc\Param(ref={Model::class}, withoutField="message_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getUserIdsAttr"}, field="user_ids")
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

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加用户
            if (isset($param['user_ids'])) {
                $model->userMessage()->saveAll($param['user_ids']);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 消息修改
     * @param int|array $ids   消息id
     * @param array     $param 消息信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
     * @Apidoc\Param(ref={Model::class,"getUserIdsAttr"}, field="user_ids")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['user_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改用户
                    if (isset($param['user_ids'])) {
                        $info = $info->append(['user_ids']);
                        model_relation_update($info, $info['user_ids'], $param['user_ids'], 'userMessage');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $param;
    }

    /**
     * 消息删除
     * @param array $ids  消息id
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
        $cache->del($ids);

        $user_message_model = new UserMessageModel();
        $user_message_pk    = $user_message_model->getPk();
        $user_message_ids   = $user_message_model->where($pk, 'in', $ids)->column($user_message_pk);
        UserMessageService::dele($user_message_ids, $real);

        return $update;
    }

    /**
     * 消息是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        $model = self::model();
        $pk    = $model->getPk();

        $user_message_model = new UserMessageModel();
        $user_message_pk    = $user_message_model->getPk();
        $user_message_ids   = $user_message_model->where($pk, 'in', $ids)->column($user_message_pk);
        UserMessageService::disable($user_message_ids, $is_disable);

        return $data;
    }

    /**
     * 消息批量修改
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
     * 消息导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => 'ID', 'width' => 12],
            ['field' => 'unique', 'name' => '编号', 'width' => 20],
            ['field' => 'title', 'name' => '标题', 'width' => 26, 'color' => 'FF0000'],
            ['field' => 'content', 'name' => '内容', 'width' => 50],
            ['field' => 'remark', 'name' => '备注', 'width' => 20],
            ['field' => $is_disable, 'name' => '禁用', 'width' => 10],
            ['field' => 'sort', 'name' => '排序', 'width' => 10],
            ['field' => 'create_time', 'name' => '添加时间', 'width' => 22],
            ['field' => 'update_time', 'name' => '修改时间', 'width' => 22],
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
     * 消息导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SYSTEM_MESSAGE;

        $field = 'unique,title,remark,is_disable,sort,create_time,update_time,content';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }
}
