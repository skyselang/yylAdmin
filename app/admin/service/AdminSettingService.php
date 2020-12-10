<?php
/*
 * @Description  : 系统设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-04
 * @LastEditTime : 2020-12-10
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\App;
use think\facade\Cache;
use app\common\cache\AdminUserCache;
use app\common\cache\AdminSettingCache;

class AdminSettingService
{
    // 默认设置id
    private static $admin_setting_id = 1;

    /**
     * 缓存设置
     * 
     * @param array  $param  缓存参数
     * @param string $method 请求方式
     *
     * @return array|bool
     */
    public static function settingCache($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $cache = Cache::handler();

            $data = $cache->info();

            $byte['type']  = 'B';
            $byte['value'] = $data['used_memory_lua'];

            $data['used_memory_lua_human'] = AdminToolService::byteTran($byte)['KB'] . 'K';
            $data['uptime_in_days']        = $data['uptime_in_days'] . '天';

            return $data;
        } else {
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

            $data['msg'] = '缓存已清空';
            $data['res'] = $res;

            return $data;
        }
    }

    /**
     * 验证码设置
     *
     * @param array  $param  验证码参数
     * @param string $method 请求方式
     *
     * @return array
     */
    public static function settingVerify($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $admin_setting = self::admin_setting();
            $admin_verify  = $admin_setting['admin_verify'];
        } else {
            $admin_setting_id = self::$admin_setting_id;

            $admin_verify['switch'] = $param['switch'];
            $admin_verify['curve']  = $param['curve'];
            $admin_verify['noise']  = $param['noise'];
            $admin_verify['bgimg']  = $param['bgimg'];
            $admin_verify['type']   = $param['type'];
            $admin_verify['length'] = $param['length'];
            $admin_verify['expire'] = $param['expire'];

            $update['admin_verify'] = serialize($admin_verify);
            $update['update_time']  = date('Y-m-d H:i:s');

            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($update);

            if (empty($admin_setting)) {
                exception();
            } else {
                AdminSettingCache::del($admin_setting_id);
            }
        }

        return $admin_verify;
    }

    /**
     * Token设置
     *
     * @param array  $param  token参数
     * @param string $method 请求方式
     *
     * @return array
     */
    public static function settingToken($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $admin_setting = self::admin_setting();
            $admin_token   = $admin_setting['admin_token'];
        } else {
            $admin_setting_id = self::$admin_setting_id;

            $admin_token['iss'] = $param['iss'];
            $admin_token['exp'] = $param['exp'];

            $update['admin_token'] = serialize($admin_token);
            $update['update_time'] = date('Y-m-d H:i:s');

            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($update);

            if (empty($admin_setting)) {
                exception();
            } else {
                AdminSettingCache::del($admin_setting_id);
            }
        }

        return $admin_token;
    }

    /**
     * 默认设置
     *
     * @return array
     */
    public static function admin_setting()
    {
        $admin_setting_id = self::$admin_setting_id;

        $admin_setting = AdminSettingCache::get($admin_setting_id);

        if (empty($admin_setting)) {
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->find();

            if (empty($admin_setting)) {
                $admin_setting['admin_setting_id'] = $admin_setting_id;
                $admin_setting['admin_verify']     = serialize([]);
                $admin_setting['admin_token']      = serialize([]);
                $admin_setting['create_time']      = date('Y-m-d H:i:s');
                Db::name('admin_setting')->insert($admin_setting);
            }

            // 验证码
            $admin_verify = unserialize($admin_setting['admin_verify']);
            if (empty($admin_verify)) {
                $admin_verify['switch'] = false;  //开关
                $admin_verify['curve']  = false;  //曲线
                $admin_verify['noise']  = false;  //杂点 
                $admin_verify['bgimg']  = false;  //背景图
                $admin_verify['type']   = 1;      //类型：1数字，2字母，3数字字母，4算术，5中文
                $admin_verify['length'] = 4;      //位数3-6位
                $admin_verify['expire'] = 180;    //有效时间（秒）
            }

            // Token
            $admin_token = unserialize($admin_setting['admin_token']);
            if (empty($admin_token)) {
                $admin_token['iss'] = 'yylAdmin';  //签发者
                $admin_token['exp'] = 12;          //有效时间（小时）
            }

            $admin_setting['admin_verify'] = serialize($admin_verify);
            $admin_setting['admin_token']  = serialize($admin_token);
            $admin_setting['update_time']  = date('Y-m-d H:i:s');
            Db::name('admin_setting')->where('admin_setting_id', $admin_setting_id)->update($admin_setting);

            AdminSettingCache::set($admin_setting_id, $admin_setting);

            $admin_setting['admin_verify'] = $admin_verify;
            $admin_setting['admin_token']  = $admin_token;
        } else {
            $admin_setting['admin_verify'] = unserialize($admin_setting['admin_verify']);
            $admin_setting['admin_token']  = unserialize($admin_setting['admin_token']);
        }

        return $admin_setting;
    }

    /**
     * 服务器信息
     *
     * @return array
     */
    public static function serverInfo()
    {
        $server_key  = 'server';
        $server_info = AdminSettingCache::get($server_key);

        if (empty($server_info)) {
            $cache = Cache::handler();
            $mysql = Db::query('select version() as version');

            $server_info['system_info']         = php_uname('s') . ' ' . php_uname('r');     //os
            $server_info['server_software']     = $_SERVER['SERVER_SOFTWARE'];               //web
            $server_info['mysql']               = $mysql[0]['version'];                      //mysql
            $server_info['php_version']         = PHP_VERSION;                               //php
            $server_info['thinkphp']            = App::version();                            //thinkphp
            $server_info['redis']               = $cache->info()['redis_version'];           //redis
            $server_info['php_sapi_name']       = php_sapi_name();                           //php_sapi_name
            $server_info['ip']                  = $_SERVER['SERVER_ADDR'];                   //ip
            $server_info['domain']              = $_SERVER['SERVER_NAME'];                   //domain
            $server_info['port']                = $_SERVER['SERVER_PORT'];                   //port
            $server_info['server_protocol']     = $_SERVER['SERVER_PROTOCOL'];               //protocol
            $server_info['upload_max_filesize'] = get_cfg_var('upload_max_filesize');        //upload_max_filesize
            $server_info['max_execution_time']  = get_cfg_var('max_execution_time') . '秒 '; //max_execution_time
            $server_info['post_max_size']       = get_cfg_var('post_max_size');              //post_max_size

            $expire = 12 * 60 * 60;
            AdminSettingCache::set($server_key, $server_info, $expire);
        }

        return $server_info;
    }
}
