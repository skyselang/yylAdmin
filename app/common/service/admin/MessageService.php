<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 消息管理
namespace app\common\service\admin;

use think\facade\Db;
use app\common\cache\admin\MessageCache;

class MessageService
{
    // 表名
    protected static $t_name = 'admin_message';
    // 表主键
    protected static $t_pk = 'admin_message_id';

    /**
     * 消息列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        if (empty($field)) {
            $field = self::$t_pk . ',admin_user_id,title,color,sort,is_open,open_time_start,open_time_end,create_time';
        }

        if (empty($order)) {
            $order = ['is_open' => 'desc', 'sort' => 'desc', self::$t_pk => 'desc'];
        }

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $pages = ceil($count / $limit);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            if (isset($v['admin_user_id'])) {
                $admin_user = UserService::info($v['admin_user_id']);
                $list[$k]['admin_user'] = $admin_user ? $admin_user['username'] : '';
            }
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 消息信息
     *
     * @param integer $admin_message_id 消息id
     * 
     * @return array
     */
    public static function info($admin_message_id)
    {
        $admin_message = MessageCache::get($admin_message_id);
        if (empty($admin_message)) {
            $admin_message = Db::name(self::$t_name)
                ->where(self::$t_pk, $admin_message_id)
                ->find();
            if (empty($admin_message)) {
                exception('消息不存在：' . $admin_message_id);
            }

            $admin_user = UserService::info($admin_message['admin_user_id']);
            $admin_message['admin_user'] = $admin_user ? $admin_user['username'] : '';

            MessageCache::set($admin_message_id, $admin_message);
        }

        return $admin_message;
    }

    /**
     * 消息添加
     *
     * @param array $param 消息信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $admin_message_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($admin_message_id)) {
            exception();
        }

        $param[self::$t_pk] = $admin_message_id;

        return $param;
    }

    /**
     * 消息修改
     *
     * @param array $param 消息信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $admin_message_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_message_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $admin_message_id;

        MessageCache::del($admin_message_id);

        return $param;
    }

    /**
     * 消息删除
     *
     * @param array $ids 消息id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            MessageCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 消息是否开启
     *
     * @param array   $ids     消息id
     * @param integer $is_open 是否开启1是0否
     * 
     * @return array
     */
    public static function is_open($ids, $is_open = 0)
    {
        $update['is_open']     = $is_open;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            MessageCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }
}
