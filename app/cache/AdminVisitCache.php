<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-09-02
 */

namespace app\cache;

use app\utils\Datetime;
use think\facade\Cache;

class AdminVisitCache
{
    /**
     * 缓存key
     *
     * @param string $date
     * 
     * @return string
     */
    public static function key($date = '')
    {
        $key = 'adminVisit:' . $date;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $exp  有效时间
     * @param string  $date 日期
     * 
     * @return integer
     */
    public static function exp($exp = 0, $date = '')
    {
        if (empty($exp)) {
            $today = date('Y-m-d');
            if ($date == 'today' || $date == 'total' || $date == $today) {
                $exp = 30 * 60;
            } elseif ($date == 'lastWeek') {
                $this_week = Datetime::thisWeek();
                $exp = strtotime($this_week[1] . ' 23:59:59') - time();
            } elseif ($date == 'lastMonth') {
                $this_month = Datetime::thisMonth();
                $exp = strtotime($this_month[1] . ' 23:59:59') - time();
            } else {
                $exp = strtotime($today . ' 23:59:59') - time();
            }
        }

        return $exp;
    }

    /**
     * 缓存设置
     *
     * @param string  $date 日期
     * @param integer $val  统计信息
     * @param integer $exp  有效时间
     * 
     * @return integer
     */
    public static function set($date = '', $val = 0, $exp = 0)
    {
        $key = self::key($date);
        $exp = self::exp($exp, $date);

        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param string $date 日期
     * 
     * @return array
     */
    public static function get($date = '')
    {
        $key = self::key($date);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $date 日期
     * 
     * @return bool
     */
    public static function del($date = '')
    {
        $key = self::key($date);
        $res = Cache::delete($key);

        return $res;
    }
}
