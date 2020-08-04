<?php
/*
 * @Description  : 日期
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-15
 * @LastEditTime : 2020-07-29
 */

namespace app\utils;

class Datetime
{
    /**
     * 今日日期
     *
     * @return string
     */
    public static function today()
    {
        return date('Y-m-d');
    }

    /**
     * 昨日日期
     *
     * @return string
     */
    public static function yesterday()
    {
        return date('Y-m-d', strtotime("-1 day"));
    }

    /**
     * 本周开始和结束日期
     *
     * @return array
     */
    public static function thisWeek()
    {
        $N     = date('N');
        $d     = 7 - $N;
        $start = date('Y-m-d', strtotime("-{$N} day"));
        $end   = date('Y-m-d', strtotime("+{$d} day"));

        return [$start, $end];
    }

    /**
     * 本周所有日期
     *
     * @return array
     */
    public static function thisWeeks()
    {
        $N = date('N') - 1;
        $mon = strtotime("-{$N} day");
        $weeks = [];
        for ($i = 0; $i < 7; $i++) {
            $date = '';
            $date = date('Y-m-d', strtotime("+{$i} day", $mon));
            $weeks[] = $date;
        }

        return $weeks;
    }

    /**
     * 上周开始和结束日期
     *
     * @return array
     */
    public static function lastWeek()
    {
        $week  = date('Y-m-d', strtotime("-1 week"));
        $time  = strtotime($week);
        $N     = date('N', $time);
        $d     = 7 - $N;
        $N     = $N - 1;
        $start = date('Y-m-d', strtotime("-{$N} day", $time));
        $end   = date('Y-m-d', strtotime("+{$d} day", $time));

        return [$start, $end];
    }

    /**
     * 上周所有日期
     *
     * @return array
     */
    public static function lastWeeks()
    {
        $week  = date('Y-m-d', strtotime("-1 week"));
        $time  = strtotime($week);
        $N     = date('N', $time);
        $N     = $N - 1;
        $mon = strtotime("-{$N} day", $time);

        $weeks = [];
        for ($i = 0; $i < 7; $i++) {
            $date = '';
            $date = date('Y-m-d', strtotime("+{$i} day", $mon));
            $weeks[] = $date;
        }

        return $weeks;
    }

    /**
     * 月份所有日期
     *  
     * @param string $month 月份
     * 
     * @return array
     */
    public static function monthDate($month = 'thismonth')
    {
        if ($month == 'thismonth') {
            $month = date('Y-m');
        }
        if ($month == 'lastmonth') {
            $month = date('Y-m', strtotime("-1 month"));
        }

        $t =  date('t', strtotime($month));
        $time = strtotime($month);

        $dates = [];
        for ($i = 0; $i < $t; $i++) {
            $date = '';
            $date = date('Y-m-d', strtotime("+{$i} day", $time));
            $dates[] = $date;
        }

        return $dates;
    }

    /**
     * 本月开始和结束日期
     *
     * @return array
     */
    public static function thisMonth()
    {
        $ym    = date('Y-m');
        $t     = date('t');
        $start = $ym . '-01';
        $end   = $ym . '-' . $t;

        return [$start, $end];
    }

    /**
     * 上个月开始和结束日期
     *
     * @return array
     */
    public static function lastMonth()
    {
        $m     = strtotime("-1 month");
        $t     = date('t', $m);
        $start = date('Y-m', $m) . '-01';
        $end   = date('Y-m', $m) . '-' . $t;

        return [$start, $end];
    }

    /**
     * 最近天数所有日期
     *  
     * @param integer $days 天数
     * 
     * @return array
     */
    public static function daysDate($days = 1)
    {
        $start = strtotime("-{$days} day");
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $date = '';
            $date = date('Y-m-d', strtotime("+{$i} day", $start));
            $dates[] = $date;
        }

        return $dates;
    }

    /**
     * 几年前开始和结束的日期
     * 
     * @param integer $year 几年
     *
     * @return array
     */
    public static function year($year = 0)
    {
        $year  = date('Y') - $year;
        $start = $year . '-01-01';
        $end   = $year . '-12-31';

        return [$start, $end];
    }

    /**
     * 几天前到现在/昨日结束的日期
     *
     * @param integer $day 天数
     * @param bool    $now 现在或者昨天结束日期
     * 
     * @return array
     */
    public static function dayToNow($day = 1, $now = false)
    {
        $end = date('Y-m-d');
        if (!$now) {
            $end = date('Y-m-d', strtotime("-1 day"));
        }
        $start = date('Y-m-d', strtotime("-{$day} day"));

        return [$start, $end];
    }

    /**
     * 两个日期间的所有日期
     *
     * @param string $start 开始日期
     * @param string $en    结束日期
     * 
     * @return array
     */
    public static function betweenDates($start = '', $end = '')
    {
        $dt_start = strtotime($start);
        $dt_end   = strtotime($end);
        $dates    = [];
        while ($dt_start <= $dt_end) {
            $dates[]  = date('Y-m-d', $dt_start);
            $dt_start = strtotime('+1 day', $dt_start);
        }

        return $dates;
    }

    /**
     * 几天前的日期
     *
     * @param integer $days 天数
     * 
     * @return integer
     */
    public static function daysAgo($days = 1)
    {
        $date = date('Y-m-d', strtotime("-{$days} day"));

        return $date;
    }

    /**
     * 几天后的日期
     *
     * @param integer $days 天数
     * 
     * @return integer
     */
    public static function daysAfter($days = 1)
    {
        $date = date('Y-m-d H:i:s', strtotime("+{$days} day"));

        return $date;
    }

    /**
     * 天数转换成秒数
     *
     * @param integer $day 天数
     * 
     * @return integer
     */
    public static function daysToSecond($day = 1)
    {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param integer $week 周数
     * 
     * @return integer
     */
    public static function weekToSecond($week = 1)
    {
        return self::daysToSecond() * 7 * $week;
    }

    /**
     * 日期的开始时间和结束时间
     *
     * @param string $date 日期
     *
     * @return array
     */
    public static function datetime($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        return [$start, $end];
    }
}
