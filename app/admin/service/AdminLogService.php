<?php
/*
 * @Description  : 日志管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\utils\Datetime;
use app\common\cache\AdminLogCache;
use app\common\service\IpInfoService;

class AdminLogService
{
    /**
     * 日志列表
     *
     * @param array   $where 条件
     * @param integer $page  分页
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
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

        $list = Db::name('admin_log')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = '';
            $list[$k]['nickname'] = '';
            $admin_user = AdminUserService::info($v['admin_user_id']);

            if ($admin_user) {
                $list[$k]['username'] = $admin_user['username'];
                $list[$k]['nickname'] = $admin_user['nickname'];
            }

            $list[$k]['menu_name'] = '';
            $list[$k]['menu_url']  = '';
            $admin_menu = AdminMenuService::info($v['admin_menu_id']);

            if ($admin_menu) {
                $list[$k]['menu_name'] = $admin_menu['menu_name'];
                $list[$k]['menu_url']  = $admin_menu['menu_url'];
            }
        }

        $pages = ceil($count / $limit);

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
                ->find();

            if (empty($admin_log)) {
                exception('日志不存在：' . $admin_log_id);
            }

            if ($admin_log['request_param']) {
                $admin_log['request_param'] = unserialize($admin_log['request_param']);
            }

            $admin_log['username'] = '';
            $admin_log['nickname'] = '';
            $admin_user = AdminUserService::info($admin_log['admin_user_id']);

            if ($admin_user) {
                $admin_log['username'] = $admin_user['username'];
                $admin_log['nickname'] = $admin_user['nickname'];
            }

            $admin_log['menu_name'] = '';
            $admin_log['menu_url']  = '';
            $admin_menu = AdminMenuService::info($admin_log['admin_menu_id']);

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
     * @param array $param 日志数据
     * 
     * @return void
     */
    public static function add($param = [])
    {
        if ($param['request_ip']) {
            $ip_info = IpInfoService::info($param['request_ip']);

            $param['request_country']  = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city']     = $ip_info['city'];
            $param['request_area']     = $ip_info['area'];
            $param['request_region']   = $ip_info['region'];
            $param['request_isp']      = $ip_info['isp'];
        }

        $param['create_time'] = date('Y-m-d H:i:s');

        Db::name('admin_log')->strict(false)->insert($param);
    }

    /**
     * 日志修改
     *
     * @param array $param 日志数据
     * 
     * @return array
     */
    public static function edit($param = [])
    {
        $admin_log_id = $param['admin_log_id'];

        unset($param['admin_log_id']);

        $param['request_param'] = serialize($param['request_param']);
        $param['update_time']   = date('Y-m-d H:i:s');

        $res = Db::name('admin_log')
            ->where('admin_log_id', $admin_log_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        $param['admin_log_id'] = $admin_log_id;

        AdminLogCache::del($admin_log_id);

        return $param;
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
        $update['is_delete']   = 1;
        $update['delete_time'] = date('Y-m-d H:i:s');

        $res = Db::name('admin_log')
            ->where('admin_log_id', $admin_log_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_log_id'] = $admin_log_id;

        AdminLogCache::del($admin_log_id);

        return $update;
    }

    /**
     * 日志数量统计
     *
     * @param string $date 日期
     *
     * @return integer
     */
    public static function staNumber($date = 'total')
    {
        $key  = $date;
        $data = AdminLogCache::get($key);

        if (empty($data)) {
            $where[] = ['is_delete', '=', 0];

            if ($date == 'total') {
                $where[] = ['admin_log_id', '>', 0];
            } else {
                if ($date == 'yesterday') {
                    $yesterday = Datetime::yesterday();
                    list($sta_time, $end_time) = Datetime::datetime($yesterday);
                } elseif ($date == 'thisweek') {
                    list($start, $end) = Datetime::thisWeek();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastweek') {
                    list($start, $end) = Datetime::lastWeek();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'thismonth') {
                    list($start, $end) = Datetime::thisMonth();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastmonth') {
                    list($start, $end) = Datetime::lastMonth();
                    $sta_time = Datetime::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = Datetime::datetime($end);
                    $end_time = $end_time[1];
                } else {
                    $today = Datetime::today();
                    list($sta_time, $end_time) = Datetime::datetime($today);
                }

                $where[] = ['create_time', '>=', $sta_time];
                $where[] = ['create_time', '<=', $end_time];
            }

            $data = Db::name('admin_log')
                ->field('admin_log_id')
                ->where($where)
                ->count('admin_log_id');

            AdminLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 日志日期统计
     *
     * @param array $date 日期范围
     *
     * @return array
     */
    public static function staDate($date = [])
    {
        if (empty($date)) {
            $date[0] = Datetime::daysAgo(31);
            $date[1] = Datetime::daysAgo(1);

            $sta_date = $date[0];
            $end_date = $date[1];

            $date_days = Datetime::betweenDates($sta_date, $end_date);
        } else {
            $sta_date = $date[0];
            $end_date = $date[1];

            $date_days = Datetime::betweenDates($sta_date, $end_date);
        }

        $key  = 'date:' . $sta_date . '-' . $end_date;
        $data = AdminLogCache::get($key);

        if (empty($data)) {
            $x_data = [];
            $y_data = [];

            foreach ($date_days as $k => $v) {
                $x_data[] = $v;

                $y_data[] = Db::name('admin_log')
                    ->field('admin_log_id')
                    ->where('is_delete', '=', 0)
                    ->where('create_time', '>=', $v . ' 00:00:00')
                    ->where('create_time', '<=', $v . ' 23:59:59')
                    ->count('admin_log_id');
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['date']   = $date;

            AdminLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 日志地区统计
     *
     * @param integer $date   日期范围
     * @param string  $region 地区类型
     * @param integer $top    top排行
     *   
     * @return array
     */
    public static function staRegion($date = [], $region = 'city', $top = 20)
    {
        if (empty($date)) {
            $date[0] = Datetime::daysAgo(31);
            $date[1] = Datetime::daysAgo(1);

            $sta_date = $date[0];
            $end_date = $date[1];
        } else {
            $sta_date = $date[0];
            $end_date = $date[1];
        }

        $key  = ':' . $sta_date . '-' . $end_date . ':top:' . $top;

        if ($region == 'country') {
            $group = 'request_country';
            $key   = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($region == 'province') {
            $group = 'request_province';
            $key   = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($region == 'isp') {
            $group = 'request_isp';
            $key   = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } else {
            $group = 'request_city';
            $key   = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }

        $data = AdminLogCache::get($key);

        if (empty($data)) {
            $sta_time = $date[0] . ' 00:00:00';
            $end_time = $date[1] . ' 23:59:59';

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $admin_log = Db::name('admin_log')
                ->field($field . ', COUNT(admin_log_id) as y_data')
                ->where($where)
                ->group($group)
                ->order('y_data desc')
                ->limit($top)
                ->select();

            $x_data = [];
            $y_data = [];
            $p_data = [];

            foreach ($admin_log as $k => $v) {
                $x_data[] = $v['x_data'];
                $y_data[] = $v['y_data'];
                $p_data[] = ['value' => $v['y_data'], 'name' => $v['x_data']];
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['p_data'] = $p_data;
            $data['date']   = $date;

            AdminLogCache::set($key, $data);
        }

        return $data;
    }
}
