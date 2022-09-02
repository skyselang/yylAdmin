<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\admin;

use think\facade\Request;
use think\facade\Config;
use app\common\utils\IpInfoUtils;
use app\common\cache\admin\UserLogCache;
use app\common\model\admin\UserLogModel;
use app\common\model\admin\UserModel;

/**
 * 用户日志
 */
class UserLogService
{
    /**
     * 用户日志列表
     *
     * @param array  $where 条件
     * @param int    $page  分页
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',admin_user_id,username,admin_menu_id,menu_url,menu_name,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }
        $admin_super_hide_where = admin_super_hide_where();
        if ($admin_super_hide_where) {
            $where[] = $admin_super_hide_where;
        }
        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 用户日志信息
     *
     * @param int  $id   用户日志id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = UserLogCache::get($id);
        if (empty($info)) {
            $model = new UserLogModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('用户日志不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $user = UserService::info($info['admin_user_id'], false);
            $info['username_now'] = $user['username'] ?? '';

            $menu = MenuService::info($info['admin_menu_id'], false);
            $info['menu_name_now'] = $menu['menu_name'] ?? '';
            $info['menu_url_now'] = $menu['menu_url'] ?? '';

            $info['request_param'] = unserialize($info['request_param']);

            UserLogCache::set($id, $info);
        }

        return $info;
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
            // 请求参数排除字段
            $request_param = Request::param();
            $param_without = Config::get('admin.log_param_without', []);
            array_push($param_without, Config::get('admin.token_name'));
            foreach ($param_without as $v) {
                unset($request_param[$v]);
            }

            $user    = UserService::info($param['admin_user_id'] ?? 0, false);
            $menu    = MenuService::info('', false);
            $ip_info = IpInfoUtils::info();

            $param['username']         = $user['username'] ?? '';
            $param['admin_menu_id']    = $menu['admin_menu_id'] ?? 0;
            $param['menu_url']         = $menu['menu_url'] ?? '';
            $param['menu_name']        = $menu['menu_name'] ?? '';
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
            $param['user_agent']       = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $model = new UserLogModel();
            $model->strict(false)->insert($param);
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
        $model = new UserLogModel();
        $pk = $model->getPk();

        $id = $param[$pk];

        unset($param[$pk]);

        $param['request_param'] = serialize($param['request_param']);
        $param['update_time']   = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        $param[$pk] = $id;

        UserLogCache::del($id);

        return $param;
    }

    /**
     * 用户日志删除
     *
     * @param array $ids 用户日志id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update[$pk] = $ids;

        UserLogCache::del($ids);

        return $update;
    }

    /**
     * 用户日志清除
     *
     * @param array $where 清除条件
     * @param bool  $clean 清空所有
     * 
     * @return array
     */
    public static function clear($where = [], $clean = false)
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $count = 0;
        if ($clean) {
            $count = $model->where($pk, '>', 0)->delete(true);
        } else {
            if ($where) {
                $count = $model->where($where)->delete();
            }
        }

        $data['count'] = $count;
        $data['where'] = $where;

        return $data;
    }

    /**
     * 用户日志统计
     *
     * @param string $type 日期类型：day日，month月
     * @param array  $date 日期范围：[开始日期，结束日期]
     * @param string $stat 统计类型：count总计，number数量
     * 
     * @return array
     */
    public static function stat($type = 'month', $date = [], $stat = 'count')
    {
        if (empty($date)) {
            if ($type == 'day') {
                $date[0] = date('Y-m-d', strtotime('-29 days'));
                $date[1] = date('Y-m-d');
            } else {
                $date[0] = date('Y-m', strtotime('-11 months'));
                $date[1] = date('Y-m');
            }
        }
        $sta_date = $date[0];
        $end_date = $date[1];

        $key = $type . ':' . $stat . $sta_date . '-' . $end_date;
        if (admin_is_super(admin_user_id())) {
            $key = $key . 'super';
        }
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $dates = [];

            if ($type == 'day') {
                $s_time = strtotime(date('Y-m-d', strtotime($sta_date)));
                $e_time = strtotime(date('Y-m-d', strtotime($end_date)));
                while ($s_time <= $e_time) {
                    $dates[] = date('Y-m-d', $s_time);
                    $s_time = strtotime('next day', $s_time);
                }

                $field = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
                $group = "date_format(create_time,'%Y-%m-%d')";
            } else {
                $s_time = strtotime(date('Y-m-01', strtotime($sta_date)));
                $e_time = strtotime(date('Y-m-01', strtotime($end_date)));
                while ($s_time <= $e_time) {
                    $dates[] = date('Y-m', $s_time);
                    $s_time = strtotime('next month', $s_time);
                }

                $sta_date = date('Y-m-01', strtotime($sta_date));
                $end_date = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($end_date)))));

                $field = "count(create_time) as num, date_format(create_time,'%Y-%m') as date";
                $group = "date_format(create_time,'%Y-%m')";
            }

            $admin_super_hide_where = admin_super_hide_where();
            if ($admin_super_hide_where) {
                $where[] = $admin_super_hide_where;
            }

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];

            $model = new UserLogModel();
            $pk = $model->getPk();

            if ($stat == 'count') {
                $data = [
                    ['date' => 'total', 'name' => '日志', 'title' => '总数', 'count' => 0],
                    ['date' => 'online', 'name' => '1小时', 'title' => '数量', 'count' => 0],
                    ['date' => 'today', 'name' => '今天', 'title' => '新增', 'count' => 0],
                    ['date' => 'yesterday', 'name' => '昨天', 'title' => '新增', 'count' => 0],
                    ['date' => 'thisweek', 'name' => '本周', 'title' => '新增', 'count' => 0],
                    ['date' => 'lastweek', 'name' => '上周', 'title' => '新增', 'count' => 0],
                    ['date' => 'thismonth', 'name' => '本月', 'title' => '新增', 'count' => 0],
                    ['date' => 'lastmonth', 'name' => '上月', 'title' => '新增', 'count' => 0],
                ];

                foreach ($data as $k => $v) {
                    $where = [];
                    if ($admin_super_hide_where) {
                        $where[] = $admin_super_hide_where;
                    }
                    $where[] = ['is_delete', '=', 0];

                    if ($v['date'] == 'total') {
                        $where[] = [$pk, '>', 0];
                    } elseif ($v['date'] == 'online') {
                        $where[] = ['create_time', '>=', date('Y-m-d H:i:s', time() - 3600)];
                        $where[] = ['create_time', '<=', date('Y-m-d H:i:s')];
                    } else {
                        if ($v['date'] == 'yesterday') {
                            $sta_date = $end_date = date('Y-m-d', strtotime('-1 day'));
                        } elseif ($v['date'] == 'thisweek') {
                            $sta_date = date('Y-m-d', strtotime('this week'));
                            $end_date = date('Y-m-d', strtotime('last day next week +0 day'));
                        } elseif ($v['date'] == 'lastweek') {
                            $sta_date = date('Y-m-d', strtotime('last week'));
                            $end_date = date('Y-m-d', strtotime('last day last week +7 day'));
                        } elseif ($v['date'] == 'thismonth') {
                            $sta_date = date('Y-m-01');
                            $end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', strtotime('next month')))));
                        } elseif ($v['date'] == 'lastmonth') {
                            $sta_date = date('Y-m-01', strtotime('last month'));
                            $end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', time()))));
                        } else {
                            $sta_date = $end_date = date('Y-m-d');
                        }

                        $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
                        $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
                    }

                    $data[$k]['count'] = $model->where($where)->count();
                }

                UserLogCache::set($key, $data);

                return $data;
            } elseif ($stat == 'number') {
                $data['title'] = '数量';
                $add = $total = [];
                // 新增日志
                $adds = $model
                    ->field($field)
                    ->where($where)
                    ->group($group)
                    ->select()
                    ->column('num', 'date');
                // 日志总数
                foreach ($dates as $k => $v) {
                    $add[$k] = $adds[$v] ?? 0;

                    if ($type == 'month') {
                        $e_t = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($v)))));
                    } else {
                        $e_t = $v;
                    }

                    $total_where = [];
                    if ($admin_super_hide_where) {
                        $total_where[] = $admin_super_hide_where;
                    }
                    $total_where[] = ['is_delete', '=', 0];
                    $total_where[] = ['create_time', '<=', $e_t . ' 23:59:59'];
                    $total[$k] = $model->where($total_where)->count();
                }

                $series = [
                    ['name' => '日志总数', 'type' => 'line', 'data' => $total, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '新增日志', 'type' => 'line', 'data' => $add, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '操作日志', 'log_type' => 2, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '登录日志', 'log_type' => 1, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']],
                ];
                foreach ($series as $k => $v) {
                    if (isset($v['log_type'])) {
                        $series_data = $model
                            ->field($field)
                            ->where($where)
                            ->where('log_type', $v['log_type'])
                            ->group($group)
                            ->select()
                            ->column('num', 'date');
                        foreach ($dates as $kx => $vx) {
                            $series[$k]['data'][$kx] = $series_data[$vx] ?? 0;
                        }
                    }
                }
            }

            $legend = array_column($series, 'name');

            $data['type']   = $type;
            $data['date']   = $date;
            $data['legend'] = $legend;
            $data['xAxis']  = $dates;
            $data['series'] = $series;

            UserLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 用户日志统计（字段）
     *
     * @param string $type 日期类型：day，month
     * @param array  $date 日期范围：[开始日期，结束日期]
     * @param string $stat 统计字段
     * @param int    $top  top排行
     *   
     * @return array
     */
    public static function statField($type = 'month', $date = [], $stat = 'request_province', $top = 30)
    {
        if (empty($date)) {
            if ($type == 'day') {
                $date[0] = date('Y-m-d', strtotime('-29 days'));
                $date[1] = date('Y-m-d');
            } else {
                $date[0] = date('Y-m', strtotime('-11 months'));
                $date[1] = date('Y-m');
            }
        }
        $sta_date = $date[0];
        $end_date = $date[1];

        if ($type == 'month') {
            $sta_date = date('Y-m-01', strtotime($sta_date));
            $end_date = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($end_date)))));
        }

        $key = $type . ':' . $stat . $sta_date . '-' . $end_date . '-top' . $top;
        if (admin_is_super(admin_user_id())) {
            $key = $key . 'super';
        }
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $model = new UserLogModel();
            $pk = $model->getPk();

            $fields = [
                ['title' => '国家', 'field' => 'request_country'],
                ['title' => '省份', 'field' => 'request_province'],
                ['title' => '城市', 'field' => 'request_city'],
                ['title' => 'ISP', 'field' => 'request_isp'],
                ['title' => '用户', 'field' => 'admin_user_id'],
            ];
            foreach ($fields as $vf) {
                if ($stat == $vf['field']) {
                    $data['title'] = $vf['title'] . 'top' . $top;
                    $group = $vf['field'];
                    $field = $group . ' as x_data';
                }
            }

            $admin_super_hide_where = admin_super_hide_where();
            if ($admin_super_hide_where) {
                $where[] = $admin_super_hide_where;
            }

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];

            $log = $model->field($field . ', COUNT(' . $pk . ') as y_data')->where($where)->group($group)->order('y_data desc')->limit($top)->select()->toArray();

            if ($stat == 'admin_user_id') {
                $user_ids = array_column($log, 'x_data');
                $UserModel = new UserModel();
                $UserPk = $UserModel->getPk();
                $user = $UserModel->field($UserPk . ',username')->where($UserPk, 'in', $user_ids)->select()->toArray();
            }

            $x_data = $y_data = [];
            foreach ($log as $v) {
                if ($stat == 'admin_user_id') {
                    foreach ($user as $vm) {
                        if ($v['x_data'] == $vm['admin_user_id']) {
                            $v['x_data'] = $vm['username'];
                        }
                    }
                }
                $x_data[] = $v['x_data'];
                $y_data[] = $v['y_data'];
            }

            $series = [
                ['name' => '日志数量', 'type' => 'line', 'data' => $y_data, 'label' => ['show' => true, 'position' => 'top']]
            ];

            $legend = array_column($series, 'name');

            $data['type']   = $type;
            $data['date']   = $date;
            $data['legend'] = $legend;
            $data['xAxis']  = $x_data;
            $data['series'] = $series;

            UserLogCache::set($key, $data);
        }

        return $data;
    }
}
