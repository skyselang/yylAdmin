<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-09-02
 */

namespace app\admin\service;

use think\facade\Db;
use app\utils\Datetime;
use app\cache\AdminVisitCache;

class AdminVisitService
{
    /**
     * 数量统计
     *
     * @param string $date 日期
     *
     * @return integer
     */
    public static function visitCount($date = 'total')
    {
        $key  = $date;
        $data = AdminVisitCache::get($key);

        if (empty($data)) {
            if ($date == 'today') {
                $today = Datetime::today();
                list($sta_time, $end_time) = Datetime::datetime($today);
            } elseif ($date == 'yesterday') {
                $yesterday = Datetime::yesterday();
                list($sta_time, $end_time) = Datetime::datetime($yesterday);
            } elseif ($date == 'thisWeek') {
                list($start, $end) = Datetime::thisWeek();
                $sta_time = Datetime::datetime($start);
                $sta_time = $sta_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'lastWeek') {
                list($start, $end) = Datetime::lastWeek();
                $sta_time = Datetime::datetime($start);
                $sta_time = $sta_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'thisMonth') {
                list($start, $end) = Datetime::thisMonth();
                $sta_time = Datetime::datetime($start);
                $sta_time = $sta_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'lastMonth') {
                list($start, $end) = Datetime::lastMonth();
                $sta_time = Datetime::datetime($start);
                $sta_time = $sta_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            }

            if ($date == 'total') {
                $where[] = ['admin_log_id', '>', 0];
            } else {
                $where[] = ['create_time', '>=', $sta_time];
                $where[] = ['create_time', '<=', $end_time];
            }

            $data = Db::name('admin_log')
                ->field('admin_log_id')
                ->where($where)
                ->count('admin_log_id');

            AdminVisitCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 日期统计
     *
     * @param array $date 日期范围
     *
     * @return array
     */
    public static function visitDate($date = [])
    {
        if (empty($date)) {
            $date[0]  = Datetime::daysAgo(31);
            $date[1]  = Datetime::daysAgo(1);
            $sta_date  = $date[0];
            $end_date  = $date[1];
            $date_days = Datetime::betweenDates($sta_date, $end_date);
        } else {
            $sta_date  = $date[0];
            $end_date  = $date[1];
            $date_days = Datetime::betweenDates($sta_date, $end_date);
        }

        $key  = 'date:' . $sta_date . '-' . $end_date;
        $data = AdminVisitCache::get($key);

        if (empty($data)) {
            $x_data = [];
            $y_data = [];
            foreach ($date_days as $k => $v) {
                $x_data[] = $v;
                $where = [];
                $where[] = ['create_time', '>=', $v . ' 00:00:00'];
                $where[] = ['create_time', '<=', $v . ' 23:59:59'];
                $y_data[] = Db::name('admin_log')
                    ->field('admin_log_id')
                    ->where($where)
                    ->count('admin_log_id');
            }

            $data = [];
            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['date']   = $date;

            AdminVisitCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 访问统计
     *
     * @param integer $date  日期范围
     * @param string  $stats 统计类型
     * @param integer $top   top排行
     *   
     * @return array
     */
    public static function visitStats($date = [], $stats = 'city', $top = 20)
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
        if ($stats == 'country') {
            $group = 'request_country';
            $key = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($stats == 'province') {
            $group = 'request_province';
            $key = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } elseif ($stats == 'isp') {
            $group = 'request_isp';
            $key = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        } else {
            $group = 'request_city';
            $key = $group . $key;
            $field = $group . ' as x_data';
            $where[] = [$group, '<>', ''];
        }

        $data = AdminVisitCache::get($key);

        if (empty($data)) {
            $sta_time = $date[0] . ' 00:00:00';
            $end_time = $date[1] . ' 23:59:59';

            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $data = Db::name('admin_log')
                ->field($field . ', COUNT(admin_log_id) as y_data')
                ->where($where)
                ->group($group)
                ->order('y_data desc')
                ->limit($top)
                ->select();

            $x_data = [];
            $y_data = [];
            foreach ($data as $k => $v) {
                $x_data[] = $v['x_data'];
                $y_data[] = $v['y_data'];
                $p_data[] = ['value' => $v['y_data'], 'name' => $v['x_data']];
            }

            $data = [];
            $data['x_data'] = $x_data;
            $data['y_data'] = $y_data;
            $data['p_data'] = $p_data;
            $data['date']   = $date;

            AdminVisitCache::set($key, $data);
        }

        return $data;
    }
}
