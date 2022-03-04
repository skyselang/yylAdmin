<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 邮件验证码缓存
namespace app\common\cache\utils;

use think\facade\Cache;

class CaptchaEmailCache
{
    /**
     * 缓存key
     *
     * @param string $email 邮箱
     * 
     * @return string
     */
    public static function key($email)
    {
        return 'captcha-email:' . $email;
    }

    /**
     * 缓存设置
     *
     * @param string $email   邮箱
     * @param string $captcha 验证码
     * @param int    $ttl     有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($email, $setting, $ttl = 1800)
    {
        return Cache::set(self::key($email), $setting, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param string $email 邮箱
     * 
     * @return array 验证码
     */
    public static function get($email)
    {
        return Cache::get(self::key($email));
    }

    /**
     * 缓存删除
     *
     * @param string $email 邮箱
     * 
     * @return bool
     */
    public static function del($email)
    {
        return Cache::delete(self::key($email));
    }
}
