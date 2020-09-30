<?php
/*
 * @Description  : 日志管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2020-09-29
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\AdminLogCache;

class AdminLogService
{
    /**
     * 日志列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param string  $field 字段
     * @param array   $order 排序
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [])
    {
        if (empty($field)) {
            $field = 'admin_log_id,admin_user_id,admin_menu_id,request_method,request_ip,request_region,request_isp,create_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['admin_log_id' => 'desc'];
        }

        $count = Db::name('admin_log')
            ->where($where)
            ->count('admin_log_id');

        $pages = ceil($count / $limit);

        $list = Db::name('admin_log')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $admin_menu_id = array_column($list, 'admin_menu_id');
        $admin_menu_id = array_unique($admin_menu_id);
        $admin_menu = Db::name('admin_menu')
            ->field('admin_menu_id,menu_url,menu_name')
            ->where('admin_menu_id', 'in', $admin_menu_id)
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
                if ($v['admin_menu_id'] == $vm['admin_menu_id']) {
                    $list[$k]['menu_name'] = $vm['menu_name'];
                    $list[$k]['menu_url']  = $vm['menu_url'];
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
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 日志信息
     *
     * @param integer $admin_log_id 日志id
     * 
     * @return array
     */
    public static function info($admin_log_id)
    {
        $admin_log = AdminLogCache::get($admin_log_id);

        if (empty($admin_log)) {
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

            $admin_user = AdminUserService::info($admin_log['admin_user_id']);
            $admin_log['username'] = '';
            $admin_log['nickname'] = '';

            if ($admin_user) {
                $admin_log['username'] = $admin_user['username'];
                $admin_log['nickname'] = $admin_user['nickname'];
            }

            $admin_menu = AdminMenuService::info($admin_log['admin_menu_id']);
            $admin_log['menu_name'] = '';
            $admin_log['menu_url']  = '';
            
            if ($admin_menu) {
                $admin_log['menu_name'] = $admin_menu['menu_name'];
                $admin_log['menu_url']  = $admin_menu['menu_url'];
            }

            AdminLogCache::set($admin_log_id, $admin_log);
        }

        return $admin_log;
    }

    /**
     * 日志添加
     *
     * @param array $admin_log 日志数据
     * 
     * @return void
     */
    public static function add($admin_log = [])
    {
        if ($admin_log['request_ip']) {
            $ip_info = AdminIpInfoService::info($admin_log['request_ip']);
            
            $admin_log['request_country']  = $ip_info['country'];
            $admin_log['request_province'] = $ip_info['province'];
            $admin_log['request_city']     = $ip_info['city'];
            $admin_log['request_area']     = $ip_info['area'];
            $admin_log['request_region']   = $ip_info['region'];
            $admin_log['request_isp']      = $ip_info['isp'];
        }

        $admin_log['create_time'] = date('Y-m-d H:i:s');

        Db::name('admin_log')->strict(false)->insert($admin_log);
    }

    /**
     * 日志删除
     *
     * @param integer $admin_log_id 日志id
     * 
     * @return array
     */
    public static function dele($admin_log_id)
    {
        $data['is_delete']   = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');
        
        $update = Db::name('admin_log')
            ->where('admin_log_id', $admin_log_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        $data['admin_log_id'] = $admin_log_id;

        AdminLogCache::del($admin_log_id);

        return $data;
    }
}
