<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员日志
namespace app\common\service;

use think\facade\Db;
use think\facade\Request;
use app\common\utils\DatetimeUtils;
use app\common\cache\MemberLogCache;
use app\common\utils\IpInfoUtils;
use app\common\model\MemberModel;

class MemberLogService
{
    // 表名
    protected static $t_name = 'member_log';
    // 表主键
    protected static $t_pk = 'member_log_id';

    /**
     * 会员日志列表
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
            $field = self::$t_pk . ',member_id,api_id,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = [self::$t_pk => 'desc'];
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
            if (isset($v['member_id'])) {
                $list[$k]['username'] = '';
                $list[$k]['nickname'] = '';
                $admin_user = MemberService::info($v['member_id']);
                if ($admin_user) {
                    $list[$k]['username'] = $admin_user['username'];
                    $list[$k]['nickname'] = $admin_user['nickname'];
                }
            }

            if (isset($v['api_id'])) {
                $list[$k]['api_name'] = '';
                $list[$k]['api_url']  = '';
                $api = ApiService::info($v['api_id']);
                if ($api) {
                    $list[$k]['api_name'] = $api['api_name'];
                    $list[$k]['api_url']  = $api['api_url'];
                }
            }
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 会员日志信息
     *
     * @param integer $member_log_id 会员日志id
     * 
     * @return array
     */
    public static function info($member_log_id)
    {
        $member_log = MemberLogCache::get($member_log_id);
        if (empty($member_log)) {
            $member_log = Db::name(self::$t_name)
                ->where(self::$t_pk, $member_log_id)
                ->find();
            if (empty($member_log)) {
                exception('会员日志不存在：' . $member_log_id);
            }
            if ($member_log['request_param']) {
                $member_log['request_param'] = unserialize($member_log['request_param']);
            }

            $member_log['username'] = '';
            $member_log['nickname'] = '';
            $admin_user = MemberService::info($member_log['member_id']);
            if ($admin_user) {
                $member_log['username'] = $admin_user['username'];
                $member_log['nickname'] = $admin_user['nickname'];
            }

            $member_log['api_name'] = '';
            $member_log['api_url']  = '';
            $api = ApiService::info($member_log['api_id']);
            if ($api) {
                $member_log['api_name'] = $api['api_name'];
                $member_log['api_url']  = $api['api_url'];
            }

            MemberLogCache::set($member_log_id, $member_log);
        }

        return $member_log;
    }

    /**
     * 会员日志添加
     *
     * @param array   $param    会员日志信息
     * @param integer $log_type 日志类型1注册2登录3操作4退出
     * 
     * @return void
     */
    public static function add($param = [], $log_type = 3)
    {
        // 会员日记是否开启
        if (member_log_switch()) {
            if ($log_type == 1) {
                $param['response_code'] = 200;
                $param['response_msg']  = '注册成功';
            } elseif ($log_type == 2) {
                $param['response_code'] = 200;
                $param['response_msg']  = '登录成功';
            } elseif ($log_type == 4) {
                $param['response_code'] = 200;
                $param['response_msg']  = '退出成功';
            }

            $api_info      = ApiService::info();
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

            $param['api_id']           = $api_info['api_id'];
            $param['log_type']         = $log_type;
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

            Db::name(self::$t_name)->strict(false)->insert($param);
        }
    }

    /**
     * 会员日志修改
     *
     * @param array $param 会员日志信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $member_log_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $member_log_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $member_log_id;

        MemberLogCache::del($member_log_id);

        return $param;
    }

    /**
     * 会员日志删除
     *
     * @param array $ids 会员日志id
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
            MemberLogCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 会员日志清除
     *
     * @param array   $where 清除条件
     * @param boolean $clean 清空所有
     * 
     * @return array
     */
    public static function clear($where = [], $clean = false)
    {
        if ($clean) {
            $count = Db::name(self::$t_name)->delete(true);
        } else {
            $count = Db::name(self::$t_name)->where($where)->delete();
        }

        $data['count'] = $count;
        $data['where'] = $where;

        return $data;
    }

    /**
     * 会员日志数量统计
     *
     * @param string $date 日期
     *
     * @return integer
     */
    public static function statNum($date = 'total')
    {
        $key  = 'num:' . $date;
        $data = MemberLogCache::get($key);
        if (empty($data)) {
            $where[] = ['is_delete', '=', 0];
            if ($date == 'total') {
                $where[] = [self::$t_pk, '>', 0];
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

            $data = Db::name(self::$t_name)
                ->field(self::$t_pk)
                ->where($where)
                ->count(self::$t_pk);

            MemberLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员日志日期统计
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
        $data = MemberLogCache::get($key);
        if (empty($data)) {
            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            $field   = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];
            $group   = "date_format(create_time,'%Y-%m-%d')";

            $member_log = Db::name(self::$t_name)
                ->field($field)
                ->where($where)
                ->group($group)
                ->select();

            $x_data = DatetimeUtils::betweenDates($sta_date, $end_date);
            $y_data = [];
            foreach ($x_data as $k => $v) {
                $y_data[$k] = 0;
                foreach ($member_log as $vu) {
                    if ($v == $vu['date']) {
                        $y_data[$k] = $vu['num'];
                    }
                }
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['date']   = $date;

            MemberLogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员日志字段统计
     *
     * @param integer $date 日期范围
     * @param string  $type 字段类型
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

        if ($type == 'country') {
            $group = 'request_country';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($type == 'province') {
            $group = 'request_province';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($type == 'isp') {
            $group = 'request_isp';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($type == 'city') {
            $group = 'request_city';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } else {
            $group = 'member_id';
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }

        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'field:' . 'top' . $top . $type . '-' . $sta_date . '-' . $end_date;
        $data = MemberLogCache::get($key);
        if (empty($data)) {
            $sta_time = DatetimeUtils::dateStartTime($date[0]);
            $end_time = DatetimeUtils::dateEndTime($date[1]);

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $member_log = Db::name(self::$t_name)
                ->field($field . ', COUNT(' . self::$t_pk . ') as y_data')
                ->where($where)
                ->group($group)
                ->order('y_data desc')
                ->limit($top)
                ->select()
                ->toArray();

            if ($type == 'member') {
                $member_ids = array_column($member_log, 'x_data');
                $Member = new MemberModel();
                $member = $Member
                    ->field('member_id,username')
                    ->where('member_id', 'in', $member_ids)
                    ->select()
                    ->toArray();
            }

            $x_data = [];
            $y_data = [];
            $p_data = [];
            foreach ($member_log as $v) {
                if ($type == 'member') {
                    foreach ($member as $km => $vm) {
                        if ($v['x_data'] == $vm['member_id']) {
                            $v['x_data'] = $vm['username'];
                        }
                    }
                }

                $x_data[] = $v['x_data'];
                $y_data[] = $v['y_data'];
                $p_data[] = ['value' => $v['y_data'], 'name' => $v['x_data']];
            }

            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['p_data'] = $p_data;
            $data['date']   = $date;

            MemberLogCache::set($key, $data);
        }

        return $data;
    }
}
