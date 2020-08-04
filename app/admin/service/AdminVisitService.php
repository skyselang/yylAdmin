<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-08-04
 */

namespace app\admin\service;

use think\facade\Db;
use app\utils\Datetime;
use app\cache\AdminVisitCache;

class AdminVisitService
{
    /**
     * 访问量
     *
     * @param string $date
     *
     * @return integer
     */
    public static function count($date = 'total')
    {
        $key   = $date;
        $count = AdminVisitCache::get($key);

        if (empty($count)) {
            if ($date == 'today') {
                $today = Datetime::today();
                list($start_time, $end_time) = Datetime::datetime($today);
            } elseif ($date == 'yesterday') {
                $yesterday = Datetime::yesterday();
                list($start_time, $end_time) = Datetime::datetime($yesterday);
            } elseif ($date == 'thisWeek') {
                list($start, $end) = Datetime::thisWeek();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'lastWeek') {
                list($start, $end) = Datetime::lastWeek();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'thisMonth') {
                list($start, $end) = Datetime::thisMonth();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'lastMonth') {
                list($start, $end) = Datetime::lastMonth();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            }

            if ($date == 'total') {
                $where[] = ['admin_log_id', '>', 0];
            } else {
                $where[] = ['create_time', '>=', $start_time];
                $where[] = ['create_time', '<=', $end_time];
            }

            $count = Db::name('admin_log')
                ->field('admin_log_id')
                ->where($where)
                ->count('admin_log_id');

            AdminVisitCache::set($key, $count);
        }

        return $count;
    }

    /**
     * date
     *
     * @param array $dates
     *
     * @return array
     */
    public static function date($dates = [])
    {
        if (empty($dates)) {
            $dates[0]  = Datetime::daysAgo(31);
            $dates[1]  = Datetime::daysAgo(1);
            $sta_date  = $dates[0];
            $end_date  = $dates[1];
            $date_days = Datetime::betweenDates($sta_date, $end_date);
        } else {
            $sta_date  = $dates[0];
            $end_date  = $dates[1];
            $date_days = Datetime::betweenDates($sta_date, $end_date);
        }

        $key = 'date:' . $sta_date . '-' . $end_date;
        $res =  AdminVisitCache::get($key);

        if (empty($res)) {
            $date = [];
            $num  = [];
            foreach ($date_days as $k => $v) {
                $count = 0;
                $where = [];
                $where[] = ['create_time', '>=', $v . ' 00:00:00'];
                $where[] = ['create_time', '<=', $v . ' 23:59:59'];
                $count = Db::name('admin_log')
                    ->field('admin_log_id')
                    ->where($where)
                    ->count('admin_log_id');
                $date[] = $v;
                $num[]  = $count;
            }

            $res = [];
            $res['date']  = $date;
            $res['num']   = $num;
            $res['dates'] = $dates;

            AdminVisitCache::set($key, $res);
        }

        return $res;
    }

    /**
     * city
     *
     * @param integer $dates
     * @param integer $top
     *   
     * @return array
     */
    public static function city($dates = [], $top = 20)
    {
        if (empty($dates)) {
            $dates[0] = Datetime::daysAgo(31);
            $dates[1] = Datetime::daysAgo(1);
            $sta_date = $dates[0];
            $end_date = $dates[1];
        } else {
            $sta_date = $dates[0];
            $end_date = $dates[1];
        }

        $key = 'city:' . $sta_date . '-' . $end_date . ':top:' . $top;
        $res = AdminVisitCache::get($key);

        if (empty($res)) {
            $sta_time = $dates[0] . ' 00:00:00';
            $end_time = $dates[1] . ' 23:59:59';
            $where[] = ['request_city', '<>', ''];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $res = Db::name('admin_log')
                ->field('request_city as city, COUNT(admin_log_id) as num')
                ->where($where)
                ->group('request_city')
                ->order('num desc')
                ->limit($top)
                ->select();

            $city = [];
            $num  = [];
            foreach ($res as $k => $v) {
                $city[] = $v['city'];
                $num[]  = $v['num'];
            }

            $res = [];
            $res['city']  = $city;
            $res['num']   = $num;
            $res['dates'] = $dates;

            AdminVisitCache::set($key, $res);
        }

        return $res;
    }

    /**
     * isp
     *
     * @param integer $dates
     * @param integer $top
     *
     * @return array
     */
    public static function isp($dates = [], $top = 20)
    {
        if (empty($dates)) {
            $dates[0] = Datetime::daysAgo(31);
            $dates[1] = Datetime::daysAgo(1);
            $sta_date = $dates[0];
            $end_date = $dates[1];
        } else {
            $sta_date = $dates[0];
            $end_date = $dates[1];
        }

        $key = 'isp:' . $sta_date . '-' . $end_date . ':top:' . $top;
        $res = AdminVisitCache::get($key);

        if (empty($res)) {
            $sta_time = $dates[0] . ' 00:00:00';
            $end_time = $dates[1] . ' 23:59:59';
            $where[] = ['request_isp', '<>', ''];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $res = Db::name('admin_log')
                ->field('request_isp as isp, COUNT(admin_log_id) as num')
                ->where($where)
                ->group('request_isp')
                ->order('num desc')
                ->limit($top)
                ->select();

            $isp = [];
            $num = [];
            foreach ($res as $k => $v) {
                $isp[] = $v['isp'];
                $num[] = ['value' => $v['num'], 'name' => $v['isp']];
            }

            $res = [];
            $res['isp']   = $isp;
            $res['num']   = $num;
            $res['dates'] = $dates;

            AdminVisitCache::set($key, $res);
        }

        return $res;
    }
}
