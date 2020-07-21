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

            AdminVisitCache::set($date, $count);
        }

        return $count;
    }

    /**
     * date
     *
     * @param integer $num
     *
     * @return array
     */
    public static function date($num = 7)
    {
        $key = 'dateLately' . $num;
        $res = AdminVisitCache::get($key);

        if (empty($res)) {
            $days = Datetime::daysDate($num);
            $date = [];
            $num  = [];
            foreach ($days as $k => $v) {
                $count = 0;
                $where = [];
                $where[] = ['create_time', '>=', $v . ' 00:00:00'];
                $where[] = ['create_time', '<=', $v . ' 23:59:59'];
                $count = Db::name('admin_log')
                    ->field('admin_log_id')
                    ->where($where)
                    ->count('admin_log_id');
                $date[] = $v;
                $num[] = $count;
            }
            $res = [];
            $res['date'] = $date;
            $res['num']  = $num;

            AdminVisitCache::set($key, $res);
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
        $key = 'cityTop' . $num;
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
     * isp
     *
     * @param integer $num
     *
     * @return array
     */
    public static function isp($num = 20)
    {
        $key = 'ispTop' . $num;
        $res = AdminVisitCache::get($key);

        if (empty($res)) {
            $res = Db::name('admin_log')
                ->field('request_isp as isp, COUNT(admin_log_id) as num')
                ->where('request_isp', '<>', '')
                ->group('request_isp')
                ->order('num desc')
                ->limit($num)
                ->select();
            $isp = [];
            $num = [];
            foreach ($res as $k => $v) {
                $isp[] = $v['isp'];
                $num[] = ['value' => $v['num'], 'name' => $v['isp']];
            }

            $res = [];
            $res['isp'] = $isp;
            $res['num'] = $num;

            AdminVisitCache::set($key, $res);
        }

        return $res;
    }
}
