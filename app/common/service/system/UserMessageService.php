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
use app\common\cache\system\UserMessageCache as Cache;
use app\common\model\system\UserMessageModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;

/**
 * 用户消息
 */
class UserMessageService
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
        'user_message_id' => 0,
        'message_id'      => 0,
        'user_id/d'       => 0,
        'is_read/d'       => 0,
        'read_time'       => null,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['user_id', 'message_id', 'is_read'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("users", type="array", desc="用户", children={
     *     @Apidoc\Returned(ref={UserService::class,"info"}, field="user_id,nickname,username"),
     *   }),
     *   @Apidoc\Returned("messages", type="array", desc="消息", children={
     *     @Apidoc\Returned(ref={MessageService::class,"info"}, field="message_id,title"),
     *   }),
     * })
     */
    public static function basedata($exp = false, $param = [])
    {
        $exps     = $exp ? where_exps() : [];
        $users    = UserService::list([], 0, 0, [], 'nickname,username', false)['list'] ?? [];
        $messages = MessageService::list([], 0, 0, [], 'title', false)['list'] ?? [];

        return ['exps' => $exps, 'users' => $users, 'messages' => $messages];
    }

    /**
     * 用户消息列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="user_message_id,user_id,message_id,is_read,read_time,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class, "user"}, field="user_nickname,user_username"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getIsReadNameAttr"}, field="is_read_name"),
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
            $order = [$pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',user_id,message_id,is_read,read_time,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'message_title') {
                $message_where = [['title', $wv[1], $wv[2]], ['is_delete', '=', 0]];
                $message_list = MessageService::list($message_where, 0, 0, [], 'message_id', false)['list'] ?? [];
                $message_ids = array_column($message_list, 'message_id');
                $where[$wk] = ['message_id', 'in', $message_ids];
            }
        }
        if (user_hide_where()) {
            $where_scope[] = user_hide_where('user_id');
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'user_id')) {
            $with[] = $hidden[] = 'user';
        }
        if (strpos($field, 'message_id')) {
            $with[] = $hidden[] = 'message';
        }
        if (strpos($field, 'is_read')) {
            $append[] = 'is_read_name';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $pages = 0;
        if ($total) {
            $count = model_where($model->clone(), $where, $where_scope)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where, $where_scope);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 用户消息信息
     * @param int  $id   用户消息id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="user_message_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"user"}, field="user_nickname,user_username")
     * @Apidoc\Returned(ref={Model::class,"message"}, field="message_title,message_content")
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getIsReadNameAttr"}, field="is_read_name")
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
                    exception(lang('消息不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_read_name', 'is_disable_name'])->toArray();

            $cache->set($id, $info);
        }

        $user    = UserService::info($info['user_id'], false);
        $message = MessageService::info($info['message_id'], false);
        $info['user_nickname']   = $user['nickname'] ?? '';
        $info['user_username']   = $user['username'] ?? '';
        $info['message_title']   = $message['title'] ?? '';
        $info['message_content'] = $message['content'] ?? '';

        return $info;
    }

    /**
     * 用户消息添加
     * @param array $param 用户消息信息
     * @Apidoc\Param(ref={Model::class}, withoutField="user_message_id,is_disable,is_delete,create_uid,update_uid,delete_uid,create_time,update_time,delete_time")
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

        return $param;
    }

    /**
     * 用户消息修改
     * @param int|array $ids   用户消息id
     * @param array     $param 用户消息信息
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
        $cache->del($ids);

        return $param;
    }

    /**
     * 用户消息删除
     * @param array $ids  用户消息id
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

        return $update;
    }

    /**
     * 用户消息是否禁用
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
     * 用户消息批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $param = [$field => $value];
        if ($field === 'is_read' && $value == 1) {
            $param['read_time'] = datetime();
        }
        $data = self::edit($ids, $param);

        return $data;
    }

    /**
     * 用户消息导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $is_read    = $exp_imp == 'export' ? 'is_read_name' : 'is_read';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => 'ID', 'width' => 12],
            ['field' => 'user_id', 'name' => '用户ID', 'width' => 12],
            ['field' => 'user_nickname', 'name' => '用户昵称', 'width' => 12],
            ['field' => 'message_id', 'name' => '消息ID', 'width' => 12],
            ['field' => 'message_title', 'name' => '消息标题', 'width' => 26],
            ['field' => $is_read, 'name' => '已读', 'width' => 10],
            ['field' => 'read_time', 'name' => '已读时间', 'width' => 22],
            ['field' => $is_disable, 'name' => '禁用', 'width' => 10],
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
     * 用户消息导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SYSTEM_USER_MESSAGE;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 用户消息已读
     * @param array $where 条件
     * @param int   $limit 限制条数，0不限制
     * @Apidoc\Query(ref="searchQuery")
     */
    public static function read($where = [], $limit = 0)
    {
        $model = self::model();
        $pk    = $model->getPk();

        array_unshift($where, [$pk, '>', 0]);

        if ($limit > 0) {
            $model = $model->limit($limit);
        }
        $count = $model->where($where)->update(['is_read' => 1, 'read_time' => datetime()]);

        return ['count' => $count];
    }

    /**
     * 我的消息未读数量
     * @param array $where 条件
     */
    public static function unreadCount($where = [])
    {
        $model = self::model();

        return $model->where($where)->where('is_read', 0)->count();
    }
}
