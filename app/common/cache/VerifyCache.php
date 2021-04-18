<?php
/*
 * @Description  : 验证码缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2021-04-10
 */

namespace app\common\cache;

use think\facade\Cache;

class VerifyCache
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
        $key = 'Verify:' . $verify_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param string  $verify_id   验证码id
     * @param string  $verify_code 验证码
     * @param integer $ttl         有效时间（秒）
     * 
     * @return bool
     */
    public static function set($verify_id = '', $verify_code = '', $ttl = 0)
    {
        $key = self::key($verify_id);
        $val = $verify_code;
        if (empty($ttl)) {
            $ttl = 180;
        }

        $res = Cache::set($key, $val, $ttl);

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
