<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户日志
namespace app\common\service\admin;

use think\facade\Db;
use think\facade\Request;
use app\common\utils\IpInfoUtils;
use app\common\utils\DatetimeUtils;
use app\common\cache\admin\UserLogCache;

class UserLogService
{
    /**
     * 用户日志列表
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
            $field = 'admin_user_log_id,admin_user_id,admin_menu_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['admin_user_log_id' => 'desc'];
        }

        $count = Db::name('admin_user_log')
            ->where($where)
            ->count('admin_user_log_id');

        $list = Db::name('admin_user_log')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        foreach ($list as $k => $v) {
            if (isset($v['admin_user_id'])) {
                $list[$k]['username'] = '';
                $admin_user = UserService::info($v['admin_user_id']);
                if ($admin_user) {
                    $list[$k]['username'] = $admin_user['username'];
                }
            }

            if (isset($v['admin_menu_id'])) {
                $list[$k]['menu_name'] = '';
                $list[$k]['menu_url']  = '';
                $admin_menu = MenuService::info($v['admin_menu_id']);
                if ($admin_menu) {
                    $list[$k]['menu_name'] = $admin_menu['menu_name'];
                    $list[$k]['menu_url']  = $admin_menu['menu_url'];
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
     * 用户日志信息
     *
     * @param integer $admin_user_log_id 用户日志id
     * 
     * @return array
     */
    public static function info($admin_user_log_id)
    {
        $admin_user_log = UserLogCache::get($admin_user_log_id);

        if (empty($admin_user_log)) {
            $admin_user_log = Db::name('admin_user_log')
                ->where('admin_user_log_id', $admin_user_log_id)
                ->find();
            if (empty($admin_user_log)) {
                exception('用户日志不存在：' . $admin_user_log_id);
            }
            if ($admin_user_log['request_param']) {
                $admin_user_log['request_param'] = unserialize($admin_user_log['request_param']);
            }

            $admin_user_log['username'] = '';
            $admin_user_log['nickname'] = '';
            $admin_user = UserService::info($admin_user_log['admin_user_id']);
            if ($admin_user) {
                $admin_user_log['username'] = $admin_user['username'];
                $admin_user_log['nickname'] = $admin_user['nickname'];
            }

            $admin_user_log['menu_name'] = '';
            $admin_user_log['menu_url']  = '';
            $admin_menu = MenuService::info($admin_user_log['admin_menu_id']);
            if ($admin_menu) {
                $admin_user_log['menu_name'] = $admin_menu['menu_name'];
                $admin_user_log['menu_url']  = $admin_menu['menu_url'];
            }

            UserLogCache::set($admin_user_log_id, $admin_user_log);
        }

        return $admin_user_log;
    }

    /**
     * 用户日志添加
     *
     * @param array $param 日志数据
     * 
     * @return void
     */
    public static function add($param = [])
    {
        // 日志记录是否开启
        if (admin_log_switch()) {
            $admin_menu    = MenuService::info();
            $ip_info       = IpInfoUtils::info();
            $request_param = Request::param();

            if (isset($request_param['password'])) {
                unset($request_param['password']);
            }
            if (isset($request_param['new_password'])) {
                unset($request_param['new_password']);
            }
            if (isset($request_param['old_password'])) {
                unset($request_param['old_password']);
            }

            $param['admin_menu_id']    = $admin_menu['admin_menu_id'];
            $param['request_ip']       = $ip_info['ip'];
            $param['request_country']  = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city']     = $ip_info['city'];
            $param['request_area']     = $ip_info['area'];
            $param['request_region']   = $ip_info['region'];
            $param['request_isp']      = $ip_info['isp'];
            $param['request_param']    = serialize($request_param);
            $param['request_method']   = Request::method();
            $param['create_time']      = datetime();

            Db::name('admin_user_log')->insert($param);
        }
    }

    /**
     * 用户日志修改
     *
     * @param array $param 用户日志
     * 
     * @return array
     */
    public static function edit($param = [])
    {
        $admin_user_log_id = $param['admin_user_log_id'];

        unset($param['admin_user_log_id']);

        $param['request_param'] = serialize($param['request_param']);
        $param['update_time']   = datetime();

        $res = Db::name('admin_user_log')
            ->where('admin_user_log_id', $admin_user_log_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param['admin_user_log_id'] = $admin_user_log_id;

        UserLogCache::del($admin_user_log_id);

        return $param;
    }

    /**
     * 用户日志删除
     *
     * @param integer $admin_user_log_id 用户日志id
     * 
     * @return array
     */
    public static function dele($admin_user_log_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('admin_user_log')
            ->where('admin_user_log_id', $admin_user_log_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['admin_user_log_id'] = $admin_user_log_id;

        UserLogCache::del($admin_user_log_id);

        return $update;
    }

    /**
     * 用户日志清除
     *
     * @param array $param 清除条件
     * 
     * @return array
     */
    public static function clear($param)
    {
        $admin_user_id = $param['admin_user_id'];
        $username      = $param['username'];
        $admin_menu_id = $param['admin_menu_id'];
        $menu_url      = $param['menu_url'];
        $date_value    = $param['date_value'];

        $where = [];
        if ($admin_user_id && $username) {
            $admin_user = Db::name('admin_user')
                ->field('admin_user_id')
                ->where('is_delete', '=', 0)
                ->where('username', '=', $username)
                ->find();
            if ($admin_user) {
                $where[] = ['admin_user_id', 'in', [$admin_user_id, $admin_user['admin_user_id']]];
            } else {
                $where[] = ['admin_user_id', '=', $admin_user_id];
            }
        } elseif ($admin_user_id) {
            $where[] = ['admin_user_id', '=', $admin_user_id];
        } elseif ($username) {
            $admin_user = Db::name('admin_user')
                ->field('admin_user_id')
                ->where('is_delete', '=', 0)
                ->where('username', '=', $username)
                ->find();
            if ($admin_user) {
                $where[] = ['admin_user_id', '=', $admin_user['admin_user_id']];
            }
        }

        if ($admin_menu_id && $menu_url) {
            $admin_menu = Db::name('admin_menu')
                ->field('admin_menu_id')
                ->where('is_delete', '=', 0)
                ->where('menu_url', '=', $menu_url)
                ->find();
            if ($admin_menu) {
                $where[] = ['admin_menu_id', 'in', [$admin_menu_id, $admin_menu['admin_menu_id']]];
            } else {
                $where[] = ['admin_menu_id', '=', $admin_menu_id];
            }
        } elseif ($admin_menu_id) {
            $where[] = ['admin_menu_id', '=', $admin_menu_id];
        } elseif ($menu_url) {
            $admin_menu = Db::name('admin_menu')
                ->field('admin_menu_id')
                ->where('is_delete', '=', 0)
                ->where('menu_url', '=', $menu_url)
                ->find();
            if ($admin_menu) {
                $where[] = ['admin_menu_id', '=', $admin_menu['admin_menu_id']];
            }
        }

        if ($date_value) {
            $sta_date = $date_value[0];
            $end_date = $date_value[1];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
        }

        $res = Db::name('admin_user_log')
            ->where($where)
            ->delete(true);

        $data['count'] = $res;
        $data['param'] = $param;

        return $data;
    }

    /**
     * 用户日志数量统计
     *
     * @param string $date 日期
     *
     * @return integer
     */
    public static function statNum($date = 'total')
    {
        $key  = 'num:' . $date;
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $where[] = ['is_delete', '=', 0];

            if ($date == 'total') {
                $where[] = ['admin_user_log_id', '>', 0];
            } else {
                if ($date == 'yesterday') {
                    $yesterday = DatetimeUtils::yesterday();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($yesterday);
                } elseif ($date == 'thisweek') {
                    list($start, $end) = DatetimeUtils::thisWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastweek') {
                    list($start, $end) = DatetimeUtils::lastWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'thismonth') {
                    list($start, $end) = DatetimeUtils::thisMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastmonth') {
                    list($start, $end) = DatetimeUtils::lastMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } else {
                    $today = DatetimeUtils::today();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($today);
                }

                $where[] = ['create_time', '>=', $sta_time];
                $where[] = ['create_time', '<=', $end_time];
            }

            $data = Db::name('admin_user_log')
                ->field('admin_user_log_id')
                ->where($where)
                ->count('admin_user_log_id');

            UserLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 用户日志日期统计
     *
     * @param array $date 日期范围
     *
     * @return array
     */
    public static function statDate($date = [])
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'date:' . $sta_date . '-' . $end_date;
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            $field   = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];
            $group   = "date_format(create_time,'%Y-%m-%d')";

            $admin_user_log = Db::name('admin_user_log')
                ->field($field)
                ->where($where)
                ->group($group)
                ->select();

            $x = DatetimeUtils::betweenDates($sta_date, $end_date);
            $s = [];

            foreach ($x as $k => $v) {
                $s[$k] = 0;
                foreach ($admin_user_log as $ka => $va) {
                    if ($v == $va['date']) {
                        $s[$k] = $va['num'];
                    }
                }
            }

            $data['x']    = $x;
            $data['s']    = $s;
            $data['date'] = $date;

            UserLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 用户日志字段统计
     *
     * @param integer $date 日期范围
     * @param string  $type 统计类型
     * @param integer $top  top排行
     *   
     * @return array
     */
    public static function statField($date = [], $type = 'city', $top = 20)
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'field:' . 'top' . $top . $type . '-' . $sta_date . '-' . $end_date;
        if ($type == 'country') {
            $group = 'request_country';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        } elseif ($type == 'province') {
            $group = 'request_province';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        } elseif ($type == 'isp') {
            $group = 'request_isp';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        } elseif ($type == 'city') {
            $group = 'request_city';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        } else {
            $group = 'admin_user_id';
            $field = $group . ' as x';
            $where[] = [$group, '<>', ''];
        }

        $data = UserLogCache::get($key);
        if (empty($data)) {
            $sta_time = DatetimeUtils::dateStartTime($date[0]);
            $end_time = DatetimeUtils::dateEndTime($date[1]);

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $admin_user_log = Db::name('admin_user_log')
                ->field($field . ', COUNT(admin_user_log_id) as s')
                ->where($where)
                ->group($group)
                ->order('s desc')
                ->limit($top)
                ->select()
                ->toArray();

            $x = $s = $sp = [];

            if ($type == 'user') {
                $admin_user_ids = array_column($admin_user_log, 'x');
                $admin_user = Db::name('admin_user')
                    ->field('admin_user_id,username')
                    ->where('admin_user_id', 'in', $admin_user_ids)
                    ->select()
                    ->toArray();
            }

            foreach ($admin_user_log as $k => $v) {
                if ($type == 'user') {
                    foreach ($admin_user as $ka => $va) {
                        if ($v['x'] == $va['admin_user_id']) {
                            $v['x'] = $va['username'];
                        }
                    }
                }

                $x[] = $v['x'];
                $s[] = $v['s'];
                $sp[] = ['value' => $v['s'], 'name' => $v['x']];
            }

            $data['x']    = $x;
            $data['s']    = $s;
            $data['sp']   = $sp;
            $data['date'] = $date;

            UserLogCache::set($key, $data);
        }

        return $data;
    }
}
