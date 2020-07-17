<?php
/*
 * @Description  : 访问
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 */

namespace app\admin\service;

use think\facade\Db;
use app\tool\Datetime;
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
        $count = AdminVisitCache::get($date);

        if (empty($count)) {
            if ($date == 'today') {
                $today = Datetime::today();
                list($start_time, $end_time) = Datetime::datetime($today);
            } elseif ($date == 'yesterday') {
                $yesterday = Datetime::yesterday();
                list($start_time, $end_time) = Datetime::datetime($yesterday);
            } elseif ($date == 'thisweek') {
                list($start, $end) = Datetime::thisWeek();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'lastweek') {
                list($start, $end) = Datetime::lastWeek();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'thismonth') {
                list($start, $end) = Datetime::thisMonth();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            } elseif ($date == 'lastmonth') {
                list($start, $end) = Datetime::lastMonth();
                $start_time = Datetime::datetime($start);
                $start_time = $start_time[0];
                $end_time = Datetime::datetime($end);
                $end_time = $end_time[1];
            }

            if ($date == 'total') {
                $where[] = ['admin_log_id', '>', 0];
            } else {
                $where[] = ['insert_time', '>=', $start_time];
                $where[] = ['insert_time', '<=', $end_time];
            }

            $count = Db::name('admin_log')
                ->field('admin_log_id')
                ->where($where)
                ->count('admin_log_id');

            AdminVisitCache::set($date, $count);
        }

        return $count;
    }

    /**
     * line
     *
     * @param integer $day
     *
     * @return array
     */
    public static function line($day = 7)
    {
        $res = AdminVisitCache::get($day);

        if (empty($res)) {
            $days = Datetime::daysDate($day);
            $date = [];
            $data = [];
            foreach ($days as $k => $v) {
                $count = 0;
                $where = [];
                $where[] = ['insert_time', '>=', $v . ' 00:00:00'];
                $where[] = ['insert_time', '<=', $v . ' 23:59:59'];
                $count = Db::name('admin_log')
                    ->field('admin_log_id')
                    ->where($where)
                    ->count('admin_log_id');
                $date[] = $v;
                $data[] = $count;
            }
            $res = [];
            $res['date'] = $date;
            $res['data'] = $data;

            AdminVisitCache::set($day, $res);
        }

        return $res;
    }

    /**
     * city
     *
     * @param integer $num
     *
     * @return array
     */
    public static function city($num = 20)
    {
        $key = 'cityTop';
        $res = AdminVisitCache::get($key);

        if (empty($res)) {
            $res = Db::name('admin_log')
                ->field('request_city as city, COUNT(admin_log_id) as num')
                ->where('request_city', '<>', '')
                ->group('request_city')
                ->order('num desc')
                ->limit($num)
                ->select();
            $city = [];
            $num  = [];
            foreach ($res as $k => $v) {
                $city[] = $v['city'];
                $num[]  = $v['num'];
            }

            $res = [];
            $res['city'] = $city;
            $res['num'] = $num;

            AdminVisitCache::set($key, $res);
        }

        return $res;
    }

    /**
     * 获取日期访问量
     *
     * @param string $date 日期
     *
     * @return integer
     */
    public static function date($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $count = AdminVisitCache::get($date);

        if (empty($count)) {
            $datetime   = Datetime::datetime($date);
            $start_time = $datetime[0];
            $end_time   = $datetime[1];

            $where[] = ['insert_time', '>=', $start_time];
            $where[] = ['insert_time', '<=', $end_time];

            $count = Db::name('admin_log')
                ->field('admin_log_id')
                ->where($where)
                ->count('admin_log_id');

            AdminVisitCache::set($date, $count);
        }

        return $count;
    }
}
