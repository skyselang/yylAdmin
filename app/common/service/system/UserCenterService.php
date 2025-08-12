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
use app\common\service\system\UserLogService;
use app\common\service\system\UserMessageService;
use app\common\model\system\UserModel as Model;

/**
 * 个人中心
 */
class UserCenterService
{
    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("log_types", type="array", desc="日志类型"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps      = $exp ? where_exps() : [];
        $log_types = SettingService::logTypes('', true);

        return ['exps' => $exps, 'log_types' => $log_types];
    }

    /**
     * 我的信息
     * @param int $user_id 用户id
     * @Apidoc\Returned(ref={UserService::class,"info"})
     */
    public static function info($user_id)
    {
        $data = UserService::info($user_id);

        unset($data['password'], $data['role_ids'], $data['menu_ids']);

        return $data;
    }

    /**
     * 我的信息修改
     * @param int   $id    用户id
     * @param array $param 用户信息
     * @Apidoc\Param(ref={UserService::class,"edit"})
     */
    public static function edit($id, $param)
    {
        return UserService::edit($id, $param);
    }

    /**
     * 我的密码修改
     * @param int   $id    用户id
     * @param array $param 用户密码
     * @Apidoc\Param("password_old", type="string", require=true, desc="旧密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
     */
    public static function pwd($id, $param)
    {
        $param['password'] = $param['password_new'];

        return UserService::edit($id, $param);
    }

    /**
     * 我的日志列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={UserLogService::class,"info"}, field="log_id,user_id,menu_id,request_url,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time"),
     *   @Apidoc\Returned(ref={MenuService::class,"info"}, field="menu_name,menu_url"),
     * })
     */
    public static function logList($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $data = UserLogService::list($where, $page, $limit, $order, $field);
        $data['basedata'] = self::basedata(true);

        return $data;
    }

    /**
     * 我的日志信息
     * @param int $log_id 日志id
     * @Apidoc\Query(ref={UserLogService::class,"info"},field="log_id")
     * @Apidoc\Returned(ref={UserLogService::class,"info"})
     */
    public static function logInfo($log_id)
    {
        $data = UserLogService::info($log_id);

        return $data;
    }

    /**
     * 我的日志删除
     * @param array $log_ids 日志id
     * @param int   $user_id 用户id
     * @Apidoc\Param(ref="idsParam")
     */
    public static function logDele($log_ids, $user_id)
    {
        $data = UserLogService::dele($log_ids, false, $user_id);

        return $data;
    }

    /**
     * 我的日志清空
     * @param array $where 条件
     * @param int   $limit 限制条数，0不限制
     * @Apidoc\Query(ref="searchQuery")
     */
    public static function logClear($where = [], $limit = 0)
    {
        $data = UserLogService::clear($where, $limit);

        return $data;
    }

    /**
     * 我的消息列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={UserMessageService::class,"info"}, field="user_message_id,user_id,message_id,is_read,read_time,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={UserMessageService::class,"info"}, field="message_title"),
     * })
     */
    public static function messageList($where = [], $page = 1, $limit = 10,  $order = [], $field = 'message_id,is_read,create_time')
    {
        $data = UserMessageService::list($where, $page, $limit, $order, $field);
        $data['basedata'] = self::basedata(true);
        unset($data['basedata']['log_types']);

        return $data;
    }

    /**
     * 我的消息信息
     * @param int $user_message_id 消息id
     * @Apidoc\Query(ref={UserMessageService::class,"info"},field="user_message_id")
     * @Apidoc\Returned(ref={UserMessageService::class,"info"})
     */
    public static function messageInfo($user_message_id)
    {
        $data = UserMessageService::info($user_message_id);

        return $data;
    }

    /**
     * 我的消息删除
     * @param array $user_message_ids 消息id
     * @param int   $user_id 用户id
     * @Apidoc\Param(ref="idsParam")
     */
    public static function messageDele($user_message_ids, $user_id)
    {
        $data = UserMessageService::dele($user_message_ids, false, $user_id);

        return $data;
    }

    /**
     * 我的消息已读
     * @param array $where 条件
     * @param int   $limit 限制条数，0不限制
     * @Apidoc\Query(ref="searchQuery")
     */
    public static function messageRead($where = [], $limit = 0)
    {
        $data = UserMessageService::read($where, $limit);

        return $data;
    }

    /**
     * 我的消息未读数量
     * @param array $where 条件
     */
    public static function messageUnreadCount($where = [])
    {
        return UserMessageService::unreadCount($where);
    }
}
