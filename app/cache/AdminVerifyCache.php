<?php
/*
 * @Description  : 验证码缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-09
 * @LastEditTime : 2020-08-13
 */

namespace app\cache;

use think\facade\Cache;
use think\facade\Config;

class AdminVerifyCache
{
    /**
     * 缓存key
     *
     * @param string $verify_id 验证码id
     * 
     * @return string
     */
    public static function key($verify_id = '')
    {
        $key = 'adminVerify:' . $verify_id;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间
     * 
     * @return integer
     */
    public static function exp($expire = 0)
    {
        if (empty($expire)) {
            $expire = Config::get('captcha.expire', 180);
        }

        return $expire;
    }

    /**
     * 缓存设置
     *
     * @param string  $verify_id   验证码id
     * @param string  $verify_code 验证码
     * @param integer $expire      有效时间
     * 
     * @return bool
     */
    public static function set($verify_id = '', $verify_code = '', $expire = 0)
    {
        $key = self::key($verify_id);
        $val = $verify_code;
        $exp = $expire ?: self::exp();
        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param string $verify_id 验证码id
     * 
     * @return string
     */
    public static function get($verify_id = '')
    {
        $key = self::key($verify_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $verify_id 验证码id
     * 
     * @return bool
     */
    public static function del($verify_id = '')
    {
        $key = self::key($verify_id);
        $res = Cache::delete($key);

        return $res;
    }
}
