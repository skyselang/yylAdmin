<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员日志
namespace app\common\service\member;

use think\facade\Request;
use think\facade\Config;
use app\common\utils\IpInfoUtils;
use app\common\cache\member\LogCache;
use app\common\service\setting\ApiService;
use app\common\model\member\LogModel;
use app\common\model\setting\ApiModel;
use app\common\model\member\MemberModel;

class LogService
{
    /**
     * 会员日志列表
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
        $model = new LogModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',member_id,username,api_id,api_name,api_url,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 会员日志信息
     *
     * @param int  $id   会员日志id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = LogCache::get($id);
        if (empty($info)) {
            $model = new LogModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('会员日志不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            if ($info['request_param']) {
                $info['request_param'] = unserialize($info['request_param']);
            }

            LogCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员日志添加
     *
     * @param array $param    会员日志信息
     * @param int   $log_type 日志类型1注册2登录3操作4退出
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

            // 请求参数排除字段
            $request_param = Request::param();
            $param_without = Config::get('api.log_param_without', []);
            array_push($param_without, Config::get('api.token_name'));
            foreach ($param_without as $v) {
                unset($request_param[$v]);
            }

            $member  = MemberService::info($param['member_id'] ?? 0, false);
            $api     = ApiService::info('', false);
            $ip_info = IpInfoUtils::info();

            $param['username']         = $member['username'] ?? '';
            $param['api_id']           = $api['api_id'] ?? 0;
            $param['api_url']          = $api['api_url'] ?? '';
            $param['api_name']         = $api['api_name'] ?? '';
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

            $model = new LogModel();
            $model->strict(false)->insert($param);
        }
    }

    /**
     * 会员日志修改
     *
     * @param array $param 会员日志信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new LogModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        LogCache::del($ids);

        return $update;
    }

    /**
     * 会员日志删除
     *
     * @param mixed $ids  会员日志id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        LogCache::del($ids);

        return $update;
    }

    /**
     * 会员日志清除
     *
     * @param array $where 清除条件
     * @param bool  $clean 清空所有
     * 
     * @return array
     */
    public static function clear($where = [], $clean = false)
    {
        $model = new LogModel();
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
     * 会员日志统计
     *
     * @param string $type 日期类型：day，month
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
        $data = LogCache::get($key);
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

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];

            $model = new LogModel();
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
                    $where = [['is_delete', '=', 0]];

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

                LogCache::set($key, $data);

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
                    $total[$k] = $model->where('is_delete', 0)->where('create_time', '<=', $e_t . ' 23:59:59')->count();
                }

                $series = [
                    ['name' => '日志总数', 'type' => 'line', 'data' => $total, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '新增日志', 'type' => 'line', 'data' => $add, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '操作日志', 'log_type' => 3, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '登录日志', 'log_type' => 2, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '注册日志', 'log_type' => 1, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']],
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

            LogCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员日志统计（字段）
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
        $data = LogCache::get($key);
        if (empty($data)) {
            $model = new LogModel();
            $pk = $model->getPk();

            $fields = [
                ['title' => '国家', 'field' => 'request_country'],
                ['title' => '省份', 'field' => 'request_province'],
                ['title' => '城市', 'field' => 'request_city'],
                ['title' => 'ISP', 'field' => 'request_isp'],
                ['title' => '会员', 'field' => 'member_id'],
            ];
            foreach ($fields as $vf) {
                if ($stat == $vf['field']) {
                    $data['title'] = $vf['title'] . 'top' . $top;
                    $group = $vf['field'];
                    $field = $group . ' as x_data';
                }
            }

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];

            $log = $model->field($field . ', COUNT(' . $pk . ') as y_data')->where($where)->group($group)->order('y_data desc')->limit($top)->select()->toArray();

            if ($stat == 'member_id') {
                $member_ids = array_column($log, 'x_data');
                $MemberModel = new MemberModel();
                $MemberPk = $MemberModel->getPk();
                $member = $MemberModel->field($MemberPk . ',username')->where($MemberPk, 'in', $member_ids)->select()->toArray();
            }

            $x_data = $y_data = [];
            foreach ($log as $v) {
                if ($stat == 'member_id') {
                    foreach ($member as $vm) {
                        if ($v['x_data'] == $vm['member_id']) {
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

            LogCache::set($key, $data);
        }

        return $data;
    }
}
