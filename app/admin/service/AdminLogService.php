<?php
/*
 * @Description  : 日志管理
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-05-06
 */

namespace app\admin\service;

use think\facade\Db;

class AdminLogService
{
    /**
     * 日志列表
     *
     * @param array   $where 条件
     * @param string  $field 字段
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [])
    {
        if (empty($field)) {
            $field = 'admin_log_id,admin_user_id,menu_url,request_method,request_ip,insert_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['admin_log_id' => 'desc'];
        }

        $count = Db::name('admin_log')
            ->where($where)
            ->count('admin_log_id');

        $list = Db::name('admin_log')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $menu_url = array_column($list, 'menu_url');
        $menu_url = array_unique($menu_url);
        $admin_menu = Db::name('admin_menu')
            ->field('menu_url,menu_name')
            ->where('menu_url', 'in', $menu_url)
            ->select()
            ->toArray();

        $admin_user_id = array_column($list, 'admin_user_id');
        $admin_user_id = array_unique($admin_user_id);
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id,username,nickname')
            ->where('admin_user_id', 'in', $admin_user_id)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            foreach ($admin_menu as $km => $vm) {
                if ($v['menu_url'] == $vm['menu_url']) {
                    $list[$k]['menu_name'] = $vm['menu_name'];
                }
            }

            foreach ($admin_user as $ku => $vu) {
                if ($v['admin_user_id'] == $vu['admin_user_id']) {
                    $list[$k]['username'] = $vu['username'];
                    $list[$k]['nickname'] = $vu['nickname'];
                }
            }
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['list'] = $list;

        return $data;
    }

    /**
     * 日志添加
     *
     * @param array $admin_log 日志数据
     * @return void
     */
    public static function add($admin_log = [])
    {
        Db::name('admin_log')->strict(false)->insert($admin_log);
    }

    /**
     * 日志信息
     *
     * @param integer $admin_log_id 日志id
     * @return array
     */
    public static function info($admin_log_id)
    {
        $admin_log = Db::name('admin_log')
            ->where('admin_log_id', $admin_log_id)
            ->where('is_delete', 0)
            ->find();
        if (empty($admin_log)) {
            error('日志不存在');
        }

        if ($admin_log['request_param']) {
            $admin_log['request_param'] = unserialize($admin_log['request_param']);
        }

        $admin_user = Db::name('admin_user')
            ->field('username,nickname')
            ->where('admin_user_id', $admin_log['admin_user_id'])
            ->find();
        if ($admin_user) {
            $admin_log['username'] = $admin_user['username'];
            $admin_log['nickname'] = $admin_user['nickname'];
        }

        $admin_menu = Db::name('admin_menu')
            ->field('menu_name')
            ->where('menu_url', $admin_log['menu_url'])
            ->find();
        if ($admin_menu) {
            $admin_log['menu_name'] = $admin_menu['menu_name'];
        }

        return $admin_log;
    }

    /**
     * 日志删除
     *
     * @param integer $admin_log_id 日志id
     * @return array
     */
    public static function dele($admin_log_id)
    {
        $data['is_delete'] = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_log')
            ->where('admin_log_id', $admin_log_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        $data['admin_log_id'] = $admin_log_id;

        return $data;
    }
}
