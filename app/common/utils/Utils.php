<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\utils;

use app\common\cache\utils\UtilsCache;
use ip2region\Ip2Region;
use think\facade\Db;

/**
 * 工具类
 */
class Utils
{
    /**
     * 格式化字节：
     * 将字节数格式化为友好显示的字符串
     * @param int  $bytes     字节数
     * @param int  $precision 保留小数位数
     * @param bool $is_space  是否使用空格分隔
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2, $is_space = true)
    {
        $units  = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB', 'BB');
        $bytes  = max($bytes, 0);
        $pow    = floor(log($bytes) / log(1024));
        $pow    = max($pow, 0);
        $bytes /= pow(1024, $pow);

        $separator = '';
        if ($is_space) {
            $separator = ' ';
        }

        return round($bytes, $precision) . $separator . $units[$pow];
    }

    /**
     * 随机字符串
     * @param int   $length    字符长度
     * @param array $character 所用字符：1数字，2小写字母，3大写字母，4标点符号
     * @return string
     */
    public static function randomStr($length = 12, $character = [1, 2, 3])
    {
        $str_arr = [
            1 => '0123456789',
            2 => 'abcdefghijklmnopqrstuvwxyz',
            3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            4 => '`~!@#$%^&*()-_=+\|[]{};:' . "'" . '",.<>/?',
        ];

        $ori = '';
        foreach ($character as $v) {
            $ori .= $str_arr[$v];
        }
        $ori = str_shuffle($ori);

        $str = '';
        $str_len = strlen($ori) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $ori[mt_rand(0, $str_len)];
        }

        return $str;
    }

    /**
     * IP信息
     * @param string $ip IP地址
     * @return array [ip, country, province, city, area, region, isp]
     */
    public static function ipInfo($ip = '')
    {
        if ($ip === '') {
            $ip = request()->ip();
        }

        $cache   = new UtilsCache();
        $ip_key  = 'ip_info:' . $ip;
        $ip_info = $cache->get($ip_key);
        if (empty($ip_info)) {
            try {
                $ip2region = Ip2Region::newWithBuffer()->search($ip);
            } catch (\Exception $e) {
                $ip2region = '';
            }
            $ip2region = explode('|', $ip2region);

            $country = $province = $city = $area = $isp = '';
            if ($ip2region[0] ?? '') {
                $country = $ip2region[0];
            }
            if ($ip2region[2] ?? '') {
                $province = $ip2region[2];
            }
            if ($ip2region[3] ?? '') {
                $city = $ip2region[3];
            }
            if ($ip2region[4] ?? '') {
                $isp = $ip2region[4];
            }
            $region = $country . $province . $city . $area;

            $ip_info['ip']       = $ip;
            $ip_info['country']  = $country;
            $ip_info['province'] = $province;
            $ip_info['city']     = $city;
            $ip_info['area']     = $area;
            $ip_info['region']   = $region;
            $ip_info['isp']      = $isp;

            $cache->set($ip_key, $ip_info, 86400);
        }

        return $ip_info;
    }

    /**
     * 服务器信息
     * @return array
     */
    public static function serverInfo()
    {
        try {
            $mysql = Db::query('select version() as version');
            $mysql_version = $mysql[0]['version'] ?? '';
        } catch (\Exception $e) {
            $mysql_version = '';
        }

        $server['thinkphp']            = \think\facade\App::version();              //thinkphp
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

        $cache_class = new UtilsCache();
        $cache_config = $cache_class->cache()::getConfig();
        if ($cache_config['default'] === 'redis') {
            $Cache = $cache_class->cache()::handler();
            $cache = $Cache->info();

            $cache['uptime_in_days']        = $cache['uptime_in_days'] . '天';
            $cache['used_memory_lua_human'] = Utils::formatBytes($cache['used_memory_lua']);
        } elseif ($cache_config['default'] === 'memcache') {
            $Cache = $cache_class->cache()::handler();
            $cache = $Cache->getstats();

            $cache['time']           = date('Y-m-d H:i:s', $cache['time']);
            $cache['uptime']         = $cache['uptime'] / (24 * 60 * 60) . '天';
            $cache['bytes_read']     = Utils::formatBytes($cache['bytes_read']);
            $cache['bytes_written']  = Utils::formatBytes($cache['bytes_written']);
            $cache['limit_maxbytes'] = Utils::formatBytes($cache['limit_maxbytes']);
        } elseif ($cache_config['default'] === 'wincache') {
            $Cache = $cache_class->cache()::handler();

            $cache['wincache_info']['wincache_fcache_meminfo'] = wincache_fcache_meminfo();
            $cache['wincache_info']['wincache_ucache_meminfo'] = wincache_ucache_meminfo();
            $cache['wincache_info']['wincache_rplist_meminfo'] = wincache_rplist_meminfo();
        }
        $cache['cache_type'] = $cache_config['default'];

        $data = array_merge($server, $cache);

        return $data;
    }
}
