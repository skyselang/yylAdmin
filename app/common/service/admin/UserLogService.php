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

use think\facade\Request;
use app\common\utils\IpInfoUtils;
use app\common\utils\DatetimeUtils;
use app\common\cache\admin\UserLogCache;
use app\common\model\admin\UserLogModel;
use app\common\model\admin\UserModel;
use app\common\model\admin\MenuModel;

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
            $field = $pk . ',admin_user_id,admin_menu_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }
        $where[] = ['is_delete', '=', 0];
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        $UserModel = new UserModel();
        $admin_user_ids = array_column($list, 'admin_user_id');
        $admin_users = $UserModel->where('admin_user_id', 'in', $admin_user_ids)->column('username', 'admin_user_id');

        $MenuModel = new MenuModel();
        $admin_menu_ids = array_column($list, 'admin_menu_id');
        $admin_menu_urls = $MenuModel->where('admin_menu_id', 'in', $admin_menu_ids)->column('menu_url', 'admin_menu_id');
        $admin_menu_names = $MenuModel->where('admin_menu_id', 'in', $admin_menu_ids)->column('menu_name', 'admin_menu_id');

        foreach ($list as $k => $v) {
            $list[$k]['username'] = isset($admin_users[$v['admin_user_id']]) ? $admin_users[$v['admin_user_id']] : '';
            $list[$k]['menu_url'] = isset($admin_menu_urls[$v['admin_menu_id']]) ? $admin_menu_urls[$v['admin_menu_id']] : '';
            $list[$k]['menu_name'] = isset($admin_menu_names[$v['admin_menu_id']]) ? $admin_menu_names[$v['admin_menu_id']] : '';
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 用户日志信息
     *
     * @param int $id 用户日志id
     * 
     * @return array
     */
    public static function info($id)
    {
        $info = UserLogCache::get($id);
        if (empty($info)) {
            $model = new UserLogModel();
            $info = $model->find($id);
            if (empty($info)) {
                exception('用户日志不存在：' . $id);
            }
            $info = $info->toArray();

            if ($info['request_param']) {
                $info['request_param'] = unserialize($info['request_param']);
            }

            $info['username'] = '';
            $info['nickname'] = '';
            $UserModel = new UserModel();
            $UserPk = $UserModel->getPk();
            $user = UserService::info($info[$UserPk], false);
            if ($user) {
                $info['username'] = $user['username'];
                $info['nickname'] = $user['nickname'];
            }

            $info['menu_name'] = '';
            $info['menu_url']  = '';
            $MenuModel = new MenuModel();
            $MenuPk = $MenuModel->getPk();
            $menu = MenuService::info($info[$MenuPk], false);
            if ($menu) {
                $info['menu_name'] = $menu['menu_name'];
                $info['menu_url']  = $menu['menu_url'];
            }

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

            $menu    = MenuService::info();
            $ip_info = IpInfoUtils::info();

            $param['admin_menu_id']    = $menu['admin_menu_id'];
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

            $model = new UserLogModel();
            $model->insert($param);
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

        UserLogCache::del($id);

        $param[$pk] = $id;

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

        foreach ($ids as $v) {
            UserLogCache::del($v);
        }

        $update[$pk] = $ids;

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
        if ($clean) {
            $count = $model->delete(true);
        } else {
            $count = $model->where($where)->delete();
        }

        $data['count'] = $count;
        $data['where'] = $where;

        return $data;
    }

    /**
     * 用户日志数量统计
     *
     * @param string $date 日期
     *
     * @return int
     */
    public static function statNum($date = 'total')
    {
        $key = 'num:' . $date;
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $model = new UserLogModel();
            $pk = $model->getPk();

            $where[] = ['is_delete', '=', 0];

            if ($date == 'total') {
                $where[] = [$pk, '>', 0];
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

            $data = $model->field($pk)->where($where)->count($pk);

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

        $key = 'date:' . $sta_date . '-' . $end_date;
        $data = UserLogCache::get($key);
        if (empty($data)) {
            $model = new UserLogModel();

            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            $field   = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];
            $group   = "date_format(create_time,'%Y-%m-%d')";

            $user_log = $model->field($field)->where($where)->group($group)->select()->toArray();

            $x = DatetimeUtils::betweenDates($sta_date, $end_date);
            $s = [];

            foreach ($x as $k => $v) {
                $s[$k] = 0;
                foreach ($user_log as $vul) {
                    if ($v == $vul['date']) {
                        $s[$k] = $vul['num'];
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
     * @param array  $date 日期范围
     * @param string $type 统计类型
     * @param int    $top  top排行
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

        $key = 'field:' . 'top' . $top . $type . '-' . $sta_date . '-' . $end_date;
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
            $model = new UserLogModel();
            $pk = $model->getPk();

            $sta_time = DatetimeUtils::dateStartTime($date[0]);
            $end_time = DatetimeUtils::dateEndTime($date[1]);

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $user_log = $model->field($field . ', COUNT(' . $pk . ') as s')->where($where)->group($group)->order('s desc')->limit($top)->select()->toArray();

            $x = $s = $sp = [];

            if ($type == 'user') {
                $UserModel = new UserModel();
                $UserPk = $UserModel->getPk();
                $admin_user_ids = array_column($user_log, 'x');
                $user = $UserModel->field($UserPk . ',username')->where($UserPk, 'in', $admin_user_ids)->select()->toArray();
            }

            foreach ($user_log as $v) {
                if ($type == 'user') {
                    foreach ($user as $va) {
                        if ($v['x'] == $va['admin_user_id']) {
                            $v['x'] = $va['username'];
                        }
                    }
                }

                $x[]  = $v['x'];
                $s[]  = $v['s'];
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
