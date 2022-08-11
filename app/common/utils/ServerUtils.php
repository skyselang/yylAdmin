<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\utils;

use think\facade\Db;
use think\facade\Cache;

/**
 * 服务器信息
 */
class ServerUtils
{
    /**
     * 服务器信息
     *
     * @return array
     */
    public static function server()
    {
        $server_key = 'utils:server';
        $server     = Cache::get($server_key);
        if (empty($server)) {
            try {
                $MySql = Db::query('select version() as version');
                $mysql_version = $MySql[0]['version'];
            } catch (\Throwable $th) {
                $mysql_version = '';
            }

            $server['system_info']         = php_uname('s') . ' ' . php_uname('r');     //os
            $server['server_software']     = $_SERVER['SERVER_SOFTWARE'];               //web
            $server['mysql']               = $mysql_version;                            //mysql
            $server['php_version']         = PHP_VERSION;                               //php
            $server['server_protocol']     = $_SERVER['SERVER_PROTOCOL'];               //protocol
            $server['ip']                  = $_SERVER['SERVER_ADDR'];                   //ip
            $server['domain']              = $_SERVER['SERVER_NAME'];                   //domain
            $server['port']                = $_SERVER['SERVER_PORT'];                   //port
            $server['php_sapi_name']       = php_sapi_name();                           //php_sapi_name
            $server['max_execution_time']  = get_cfg_var('max_execution_time') . '秒 '; //max_execution_time
            $server['upload_max_filesize'] = get_cfg_var('upload_max_filesize');        //upload_max_filesize
            $server['post_max_size']       = get_cfg_var('post_max_size');              //post_max_size

            $server_ttl = 12 * 60 * 60;
            Cache::set($server_key, $server, $server_ttl);
        }

        $cache_key = "utils:cache";
        $cache     = Cache::get($cache_key);
        if (empty($cache)) {
            $config = Cache::getConfig();
            if ($config['default'] == 'redis') {
                $Cache = Cache::handler();
                $cache = $Cache->info();

                $cache['used_memory_lua_human'] = ByteUtils::shift($cache['used_memory_lua'], 'B')['KB'] . 'K';
                $cache['uptime_in_days']        = $cache['uptime_in_days'] . '天';
            } elseif ($config['default'] == 'memcache') {
                $Cache = Cache::handler();
                $cache = $Cache->getstats();

                $cache['time']           = date('Y-m-d H:i:s', $cache['time']);
                $cache['uptime']         = $cache['uptime'] / (24 * 60 * 60) . ' 天';
                $cache['bytes_read']     = ByteUtils::shift($cache['bytes_read'], 'B')['MB'] . ' MB';
                $cache['bytes_written']  = ByteUtils::shift($cache['bytes_written'], 'B')['MB'] . ' MB';
                $cache['limit_maxbytes'] = ByteUtils::shift($cache['limit_maxbytes'], 'B')['MB'] . ' MB';
            } elseif ($config['default'] == 'wincache') {
                $Cache = Cache::handler();

                $cache['wincache_info']['wincache_fcache_meminfo'] = wincache_fcache_meminfo();
                $cache['wincache_info']['wincache_ucache_meminfo'] = wincache_ucache_meminfo();
                $cache['wincache_info']['wincache_rplist_meminfo'] = wincache_rplist_meminfo();
            }

            $cache['type'] = $config['default'];

            $cache_ttl = 12 * 60 * 60;
            Cache::set($cache_key, $cache, $cache_ttl);
        }

        $data = array_merge($server, $cache);

        return $data;
    }
}
