<?php
/*
 * @Description  : 系统设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-04-13
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Cache;
use app\common\cache\AdminUserCache;
use app\common\cache\AdminSettingCache;

class AdminSettingService
{
    // 默认设置id
    private static $admin_setting_id = 1;
    private static $cache_key = 'cache';

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $admin_setting_id = self::$admin_setting_id;

        $admin_setting = AdminSettingCache::get($admin_setting_id);
        if (empty($admin_setting)) {
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->find();

            if (empty($admin_setting)) {
                $admin_setting['admin_setting_id'] = $admin_setting_id;
                $admin_setting['verify']           = serialize([]);
                $admin_setting['token']            = serialize([]);
                $admin_setting['create_time']      = datetime();
                Db::name('admin_setting')
                    ->insert($admin_setting);
            }

            // 验证码
            $verify = unserialize($admin_setting['verify']);
            if (empty($verify)) {
                $verify['switch'] = false;  //开关
                $verify['curve']  = false;  //曲线
                $verify['noise']  = false;  //杂点 
                $verify['bgimg']  = false;  //背景图
                $verify['type']   = 1;      //类型：1数字，2字母，3数字字母，4算术，5中文
                $verify['length'] = 4;      //位数3-6位
                $verify['expire'] = 180;    //有效时间（秒）
            }

            // Token
            $token = unserialize($admin_setting['token']);
            if (empty($token)) {
                $token['iss'] = 'yylAdmin';  //签发者
                $token['exp'] = 12;          //有效时间（小时）
            }

            $admin_setting['verify'] = serialize($verify);
            $admin_setting['token']  = serialize($token);
            $admin_setting['update_time']  = datetime();
            Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($admin_setting);

            AdminSettingCache::set($admin_setting_id, $admin_setting);

            $admin_setting['verify'] = $verify;
            $admin_setting['token']  = $token;
        } else {
            $admin_setting['verify'] = unserialize($admin_setting['verify']);
            $admin_setting['token']  = unserialize($admin_setting['token']);
        }

        $cache_key = self::$cache_key;
        $cache = AdminSettingCache::get($cache_key);
        if (empty($cache)) {
            $config = Cache::getConfig();
            if ($config['default'] == 'redis') {
                $Cache = Cache::handler();
                $cache = $Cache->info();

                $byte['type']  = 'B';
                $byte['value'] = $cache['used_memory_lua'];

                $cache['used_memory_lua_human'] = AdminUtilsService::bytetran($byte)['KB'] . 'K';
                $cache['uptime_in_days']        = $cache['uptime_in_days'] . '天';
            } elseif ($config['default'] == 'memcache') {
                $Cache = Cache::handler();
                $cache = $Cache->getstats();

                $cache['time']           = date('Y-m-d H:i:s', $cache['time']);
                $cache['uptime']         = $cache['uptime'] / (24 * 60 * 60) . ' 天';
                $cache['bytes_read']     = AdminUtilsService::bytetran(['type' => 'B', 'value' => $cache['bytes_read']])['MB'] . ' MB';
                $cache['bytes_written']  = AdminUtilsService::bytetran(['type' => 'B', 'value' => $cache['bytes_written']])['MB'] . ' MB';
                $cache['limit_maxbytes'] = AdminUtilsService::bytetran(['type' => 'B', 'value' => $cache['limit_maxbytes']])['MB'] . ' MB';
            } elseif ($config['default'] == 'wincache') {
                $Cache = Cache::handler();

                $cache['wincache_info']['wincache_fcache_meminfo'] = wincache_fcache_meminfo();
                $cache['wincache_info']['wincache_ucache_meminfo'] = wincache_ucache_meminfo();
                $cache['wincache_info']['wincache_rplist_meminfo'] = wincache_rplist_meminfo();
            }

            $cache['type'] = $config['default'];

            $cache_key = self::$cache_key;
            AdminSettingCache::set($cache_key, $cache, 30);
        }
        $admin_setting['cache'] = $cache;

        return $admin_setting;
    }

    /**
     * 缓存设置
     * 
     * @param array $param  缓存参数
     *
     * @return array
     */
    public static function cache()
    {
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('is_delete', 0)
            ->select();

        $admin_user_cache = [];
        foreach ($admin_user as $k => $v) {
            $user_cache = AdminUserCache::get($v['admin_user_id']);
            if ($user_cache) {
                $user_cache_temp['admin_user_id'] = $user_cache['admin_user_id'];
                $user_cache_temp['admin_token']   = $user_cache['admin_token'];
                $admin_user_cache[] = $user_cache_temp;
            }
        }

        $res = Cache::clear();
        if (empty($res)) {
            exception();
        }

        foreach ($admin_user_cache as $k => $v) {
            $admin_user_new = AdminUserService::info($v['admin_user_id']);
            $admin_user_new['admin_token'] = $v['admin_token'];
            AdminUserCache::set($admin_user_new['admin_user_id'], $admin_user_new);
        }

        $cache_key = self::$cache_key;
        Cache::delete($cache_key);

        $data['msg']   = '缓存已清空';
        $data['clear'] = $res;

        return $data;
    }

    /**
     * 验证码设置
     *
     * @param array $param  验证码参数
     *
     * @return array
     */
    public static function verify($param)
    {
        $admin_setting_id = self::$admin_setting_id;

        $verify['switch'] = $param['switch'];
        $verify['curve']  = $param['curve'];
        $verify['noise']  = $param['noise'];
        $verify['bgimg']  = $param['bgimg'];
        $verify['type']   = $param['type'];
        $verify['length'] = $param['length'];
        $verify['expire'] = $param['expire'];

        $update['verify']      = serialize($verify);
        $update['update_time'] = datetime();

        $res = Db::name('admin_setting')
            ->where('admin_setting_id', $admin_setting_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminSettingCache::del($admin_setting_id);

        return $verify;
    }

    /**
     * Token设置
     *
     * @param array $param token参数
     *
     * @return array
     */
    public static function token($param)
    {
        $admin_setting_id = self::$admin_setting_id;

        $token['iss'] = $param['iss'];
        $token['exp'] = $param['exp'];

        $update['token']       = serialize($token);
        $update['update_time'] = datetime();

        $res = Db::name('admin_setting')
            ->where('admin_setting_id', $admin_setting_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminSettingCache::del($admin_setting_id);

        return $token;
    }
}
