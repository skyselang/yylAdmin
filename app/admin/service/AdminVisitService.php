<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-08-14
 */

namespace app\admin\service;

use think\facade\Db;
use app\utils\Datetime;
use app\cache\AdminVisitCache;

class AdminVisitService
{
    /**
     * 访问统计
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
     * @param array $dates 日期范围
     *
     * @return array
     */
    public static function visitDate($dates = [])
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

        $key  = 'date:' . $sta_date . '-' . $end_date;
        $data = AdminVisitCache::get($key);

        if (empty($data)) {
            $date = [];
            $num  = [];
            foreach ($date_days as $k => $v) {
                $data = 0;
                $where = [];
                $where[] = ['create_time', '>=', $v . ' 00:00:00'];
                $where[] = ['create_time', '<=', $v . ' 23:59:59'];
                $data = Db::name('admin_log')
                    ->field('admin_log_id')
                    ->where($where)
                    ->count('admin_log_id');
                $date[] = $v;
                $num[]  = $data;
            }

            $data = [];
            $data['date']  = $date;
            $data['num']   = $num;
            $data['dates'] = $dates;

            AdminVisitCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 城市统计
     *
     * @param integer $dates 日期范围
     * @param integer $top   top排行
     *   
     * @return array
     */
    public static function visitCity($dates = [], $top = 20)
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

        $key  = 'city:' . $sta_date . '-' . $end_date . ':top:' . $top;
        $data = AdminVisitCache::get($key);

        if (empty($data)) {
            $sta_time = $dates[0] . ' 00:00:00';
            $end_time = $dates[1] . ' 23:59:59';
            $where[] = ['request_city', '<>', ''];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $data = Db::name('admin_log')
                ->field('request_city as city, COUNT(admin_log_id) as num')
                ->where($where)
                ->group('request_city')
                ->order('num desc')
                ->limit($top)
                ->select();

            $city = [];
            $num  = [];
            foreach ($data as $k => $v) {
                $city[] = $v['city'];
                $num[]  = $v['num'];
            }

            $data = [];
            $data['city']  = $city;
            $data['num']   = $num;
            $data['dates'] = $dates;

            AdminVisitCache::set($key, $data);
        }

        return $data;
    }

    /**
     * isp统计
     *
     * @param integer $dates 日期范围
     * @param integer $top   top排行
     *
     * @return array
     */
    public static function visitIsp($dates = [], $top = 20)
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

        $key  = 'isp:' . $sta_date . '-' . $end_date . ':top:' . $top;
        $data = AdminVisitCache::get($key);

        if (empty($data)) {
            $sta_time = $dates[0] . ' 00:00:00';
            $end_time = $dates[1] . ' 23:59:59';
            $where[] = ['request_isp', '<>', ''];
            $where[] = ['create_time', '>=', $sta_time];
            $where[] = ['create_time', '<=', $end_time];

            $data = Db::name('admin_log')
                ->field('request_isp as isp, COUNT(admin_log_id) as num')
                ->where($where)
                ->group('request_isp')
                ->order('num desc')
                ->limit($top)
                ->select();

            $isp = [];
            $num = [];
            foreach ($data as $k => $v) {
                $isp[] = $v['isp'];
                $num[] = ['value' => $v['num'], 'name' => $v['isp']];
            }

            $data = [];
            $data['isp']   = $isp;
            $data['num']   = $num;
            $data['dates'] = $dates;

            AdminVisitCache::set($key, $data);
        }

        return $data;
    }
}
