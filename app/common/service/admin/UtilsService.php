<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 实用工具
namespace app\common\service\admin;

use think\facade\Db;
use think\facade\Cache;
use app\common\utils\IpInfoUtils;

class UtilsService
{
    /**
     * 随机字符串
     *
     * @param array $ids 所用字符：1数字，2小写字母，3大写字母，4特殊符号
     * @param array $len 字符串长度
     * 
     * @return array
     */
    public static function strrand($ids = [1, 2, 3], $len = 12)
    {
        $character = [
            1 => '0123456789',
            2 => 'abcdefghijklmnopqrstuvwxyz',
            3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            4 => '`~!@#$%^&*()-_=+\|[]{};:' . "'" . '",.<>/?',
        ];

        $ori = '';
        foreach ($ids as $v) {
            $ori .= $character[$v];
        }
        $ori = str_shuffle($ori);

        $str = '';
        $str_len = strlen($ori) - 1;
        for ($i = 0; $i < $len; $i++) {
            $str .= $ori[mt_rand(0, $str_len)];
        }

        $data['ori'] = $ori;
        $data['len'] = $len;
        $data['str'] = $str;

        return $data;
    }

    /**
     * 字符串转换
     *
     * @param string $str 字符串
     *
     * @return array
     */
    public static function strtran($str = '')
    {
        if ($str == '') {
            $str = 'yylAdmin';
        }

        $rev = '';
        $len = mb_strlen($str, 'utf-8');
        for ($i = $len - 1; $i >= 0; $i--) {
            $rev = $rev . mb_substr($str, $i, 1, 'utf-8');
        }

        $data['str']   = $str;
        $data['len']   = $len;
        $data['lower'] = strtolower($str);
        $data['upper'] = strtoupper($str);
        $data['rev']   = $rev;
        $data['md5']   = md5($str);

        return $data;
    }

    /**
     * 时间戳转换
     *
     * @param array $param
     * 
     * @return array
     */
    public static function timestamp($param)
    {
        $type  = $param['type'] ?: 'timestamp';
        $value = $param['value'] ?: time();
        if ($type == 'timestamp') {
            $data['datetime']  = date('Y-m-d H:i:s', $value);
            $data['timestamp'] = $value;
        } else {
            $data['datetime']  = $value;
            $data['timestamp'] = strtotime($value);
        }

        $data['type']  = $type;
        $data['value'] = $value;

        return $data;
    }

    /**
     * 字节转换
     *
     * @param array $param 类型、数值
     *
     * @return array
     */
    public static function bytetran($param)
    {
        $type  = $param['type'] ?: 'B';
        $value = $param['value'] ?: 1024;

        $hex_b = 8;
        $hex_B = 1024;

        if ($type == 'B') {
            $data['B']  = $value;
            $data['b']  = $data['B'] * $hex_b;
            $data['KB'] = $data['B'] / $hex_B;
            $data['MB'] = $data['KB'] / $hex_B;
            $data['GB'] = $data['MB'] / $hex_B;
            $data['TB'] = $data['GB'] / $hex_B;
        } elseif ($type == 'KB') {
            $data['KB'] = $value;
            $data['B']  = $data['KB'] * $hex_B;
            $data['b']  = $data['B'] * $hex_b;
            $data['MB'] = $data['KB'] / $hex_B;
            $data['GB'] = $data['MB'] / $hex_B;
            $data['TB'] = $data['GB'] / $hex_B;
        } elseif ($type == 'MB') {
            $data['MB'] = $value;
            $data['KB'] = $data['MB'] * $hex_B;
            $data['B']  = $data['KB'] * $hex_B;
            $data['b']  = $data['B']  * $hex_b;
            $data['GB'] = $data['MB'] / $hex_B;
            $data['TB'] = $data['GB'] / $hex_B;
        } elseif ($type == 'GB') {
            $data['GB'] = $value;
            $data['MB'] = $data['GB'] * $hex_B;
            $data['KB'] = $data['MB'] * $hex_B;
            $data['B']  = $data['KB'] * $hex_B;
            $data['b']  = $data['B'] * $hex_b;
            $data['TB'] = $data['GB'] / $hex_B;
        } elseif ($type == 'TB') {
            $data['TB'] = $value;
            $data['GB'] = $data['TB'] * $hex_B;
            $data['MB'] = $data['GB'] * $hex_B;
            $data['KB'] = $data['MB'] * $hex_B;
            $data['B']  = $data['KB'] * $hex_B;
            $data['b']  = $data['B'] * $hex_b;
        } else {
            $data['b']  = $value;
            $data['B']  = $data['b'] / $hex_b;
            $data['KB'] = $data['B'] / $hex_B;
            $data['MB'] = $data['KB'] / $hex_B;
            $data['GB'] = $data['MB'] / $hex_B;
            $data['TB'] = $data['GB'] / $hex_B;
        }

        $data['type']  = $type;
        $data['value'] = $value;

        return $data;
    }

    /**
     * IP查询
     *
     * @param string $param ip、域名
     *
     * @return array
     */
    public static function ipinfo($ip = '')
    {
        return IpInfoUtils::info($ip);
    }

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

                $byte['type']  = 'B';
                $byte['value'] = $cache['used_memory_lua'];

                $cache['used_memory_lua_human'] = UtilsService::bytetran($byte)['KB'] . 'K';
                $cache['uptime_in_days']        = $cache['uptime_in_days'] . '天';
            } elseif ($config['default'] == 'memcache') {
                $Cache = Cache::handler();
                $cache = $Cache->getstats();

                $cache['time']           = date('Y-m-d H:i:s', $cache['time']);
                $cache['uptime']         = $cache['uptime'] / (24 * 60 * 60) . ' 天';
                $cache['bytes_read']     = UtilsService::bytetran(['type' => 'B', 'value' => $cache['bytes_read']])['MB'] . ' MB';
                $cache['bytes_written']  = UtilsService::bytetran(['type' => 'B', 'value' => $cache['bytes_written']])['MB'] . ' MB';
                $cache['limit_maxbytes'] = UtilsService::bytetran(['type' => 'B', 'value' => $cache['limit_maxbytes']])['MB'] . ' MB';
            } elseif ($config['default'] == 'wincache') {
                $Cache = Cache::handler();

                $cache['wincache_info']['wincache_fcache_meminfo'] = wincache_fcache_meminfo();
                $cache['wincache_info']['wincache_ucache_meminfo'] = wincache_ucache_meminfo();
                $cache['wincache_info']['wincache_rplist_meminfo'] = wincache_rplist_meminfo();
            }

            $cache['type'] = $config['default'];

            Cache::set($cache_key, $cache, 60);
        }

        $data = array_merge($server, $cache);

        return $data;
    }
}
