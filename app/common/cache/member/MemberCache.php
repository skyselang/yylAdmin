<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\member;

use think\facade\Cache;
use app\common\service\member\MemberService;
use app\common\service\member\SettingService;

/**
 * 会员管理缓存
 */
class MemberCache
{
    // 缓存标签
    protected static $tag = 'member';
    // 缓存前缀
    protected static $prefix = 'member:';

    /**
     * 缓存键名
     *
     * @param mixed $id 会员id、统计时间
     * 
     * @return string
     */
    public static function key($id)
    {
        return self::$prefix . $id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $id   会员id、统计时间
     * @param array $info 会员信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = -1)
    {
        if ($ttl === -1) {
            $set = SettingService::info();
            $ttl = $set['token_exp'] * 3600;
        }
        
        return Cache::tag(self::$tag)->set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $id 会员id、统计时间
     * 
     * @return array 会员信息
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $id 会员id、统计时间
     * 
     * @return bool
     */
    public static function del($id = '')
    {
        $ids = var_to_array($id);
        foreach ($ids as $v) {
            Cache::delete(self::key($v));
        }
        return true;
    }

    /**
     * 缓存清除
     * 
     * @return bool
     */
    public static function clear()
    {
        return Cache::tag(self::$tag)->clear();
    }

    /**
     * 缓存更新
     *
     * @param mixed $id 会员id
     * 
     * @return void
     */
    public static function upd($id)
    {
        $setting = SettingService::info();
        $ids = var_to_array($id);
        foreach ($ids as $v) {
            $old = self::get($v);
            if ($old) {
                self::del($v);
                $new = MemberService::info($v, false);
                if ($new) {
                    $new[$setting['token_name']] = $old[$setting['token_name']];
                    self::set($v, $new);
                }
            }
        }
    }
}
