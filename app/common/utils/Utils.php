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

        $data['thinkphp']            = \think\facade\App::version();              //thinkphp
        $data['system_info']         = php_uname('s') . ' ' . php_uname('r');     //os
        $data['server_software']     = $_SERVER['SERVER_SOFTWARE'];               //web
        $data['mysql']               = $mysql_version;                            //mysql
        $data['php_version']         = PHP_VERSION;                               //php
        $data['server_protocol']     = $_SERVER['SERVER_PROTOCOL'];               //protocol
        $data['ip']                  = $_SERVER['SERVER_ADDR'];                   //ip
        $data['domain']              = $_SERVER['SERVER_NAME'];                   //domain
        $data['port']                = $_SERVER['SERVER_PORT'];                   //port
        $data['php_sapi_name']       = php_sapi_name();                           //php_sapi_name
        $data['max_execution_time']  = get_cfg_var('max_execution_time') . '秒 '; //max_execution_time
        $data['upload_max_filesize'] = get_cfg_var('upload_max_filesize');        //upload_max_filesize
        $data['post_max_size']       = get_cfg_var('post_max_size');              //post_max_size
        $data['memory_limit']        = ini_get('memory_limit');                   //memory_limit
        $data['timezone']            = date_default_timezone_get();               //timezone

        $cache_class = new UtilsCache();
        $cache_config = $cache_class->cache()::getConfig();
        $data['cache_type'] = $cache_config['default']; //缓存类型
        if ($cache_config['default'] === 'file') {
            $data['cache_path'] = 'runtime/cache'; //缓存文件路径
        } elseif ($cache_config['default'] === 'redis') {
            $Cache = $cache_class->cache()::handler();
            $cache = $Cache->info();
            $data['redis_version']              = $cache['redis_version']; //redis版本
            $data['uptime_in_days']             = $cache['uptime_in_days'] . '天'; //redis运行时长
            $data['used_memory_human']          = Utils::formatBytes($cache['used_memory']); //redis已用内存
            $data['used_memory_peak_human']     = Utils::formatBytes($cache['used_memory_peak']); //redis已用内存峰值
            $data['used_memory_lua_human']      = Utils::formatBytes($cache['used_memory_lua']); //redis已用内存lua
            $data['connected_clients']          = $cache['connected_clients']; //redis当前打开链接数
            $data['total_connections_received'] = $cache['total_connections_received']; //redis曾打开连接总数
            $data['total_commands_processed']   = $cache['total_commands_processed']; //redis执行命令总数
            $data['mem_fragmentation_ratio']    = $cache['mem_fragmentation_ratio']; //redis内存碎片率
            $data['db0']                        = $cache['db0']; //redis数据库0
            for ($i = 1; $i <= 15; $i++) {
                if ($cache['db' . $i] ?? '') {
                    $data['db' . $i] = $cache['db' . $i]; //redis数据库i
                }
            }
        } elseif ($cache_config['default'] === 'memcache') {
            $Cache = $cache_class->cache()::handler();
            $cache = $Cache->getstats();
            $data['version']           = $cache['version']; //memcache版本
            $data['time']              = date('Y-m-d H:i:s', $cache['time']); //memcache当前服务器时间
            $data['uptime']            = round($cache['uptime'] / (24 * 60 * 60), 2) . '天'; //memcache运行时长
            $data['bytes_read']        = Utils::formatBytes($cache['bytes_read']); //memcache读取字节总数
            $data['bytes_written']     = Utils::formatBytes($cache['bytes_written']); //memcache写入字节总数
            $data['limit_maxbytes']    = Utils::formatBytes($cache['limit_maxbytes']); //memcache分配的内存数
            $data['curr_connections']  = $cache['curr_connections']; //memcache当前打开链接数
            $data['total_connections'] = $cache['total_connections']; //memcache曾打开连接总数
            $data['cmd_get']           = $cache['cmd_get']; //memcache执行get命令总数
            $data['cmd_set']           = $cache['cmd_set']; //memcache执行set命令总数
            $data['cmd_flush']         = $cache['cmd_flush']; //memcache执行flush_all命令总数
        } elseif ($cache_config['default'] === 'wincache') {
            $wincache_fcache_meminfo = wincache_fcache_meminfo();
            $wincache_ucache_meminfo = wincache_ucache_meminfo();
            $wincache_rplist_meminfo = wincache_rplist_meminfo();
            $data['fcache_memory_total']    = Utils::formatBytes($wincache_fcache_meminfo['memory_total']); //文件缓存总内存
            $data['fcache_memory_free']     = Utils::formatBytes($wincache_fcache_meminfo['memory_free']); //文件缓存可用内存
            $data['fcache_memory_overhead'] = Utils::formatBytes($wincache_fcache_meminfo['memory_overhead']); //文件缓存额外内存
            $data['fcache_num_used_blks']   = $wincache_fcache_meminfo['num_used_blks']; //文件缓存已用块数
            $data['fcache_num_free_blks']   = $wincache_fcache_meminfo['num_free_blks']; //文件缓存可用块数
            $data['ucache_memory_total']    = Utils::formatBytes($wincache_ucache_meminfo['memory_total']); //用户缓存总内存
            $data['ucache_memory_free']     = Utils::formatBytes($wincache_ucache_meminfo['memory_free']); //用户缓存可用内存
            $data['ucache_memory_overhead'] = Utils::formatBytes($wincache_ucache_meminfo['memory_overhead']); //用户缓存额外内存
            $data['ucache_num_used_blks']   = $wincache_ucache_meminfo['num_used_blks']; //用户缓存已用块数
            $data['ucache_num_free_blks']   = $wincache_ucache_meminfo['num_free_blks']; //用户缓存可用块数
            $data['rplist_memory_total']    = Utils::formatBytes($wincache_rplist_meminfo['memory_total']); //列表缓存总内存
            $data['rplist_memory_free']     = Utils::formatBytes($wincache_rplist_meminfo['memory_free']); //列表缓存可用内存
            $data['rplist_memory_overhead'] = Utils::formatBytes($wincache_rplist_meminfo['memory_overhead']); //列表缓存额外内存
            $data['rplist_num_used_blks']   = $wincache_rplist_meminfo['num_used_blks']; //列表缓存已用块数
            $data['rplist_num_free_blks']   = $wincache_rplist_meminfo['num_free_blks']; //列表缓存可用块数
        }

        return $data;
    }

    /**
     * 敏感数据脱敏
     * @param string $input 输入字符串
     * @return string 脱敏后字符串
     */
    public static function dataMasking($input)
    {
        // 掩码字符
        $maskChar = '*';

        // 分段比例设置
        $ratios = [
            ['max_len' => 2, 'ratio' => 0.5],           // 1-2字符：50% （特殊处理）
            ['max_len' => 5, 'ratio' => 0.2],           // 3-5字符：20%
            ['max_len' => 11, 'ratio' => 0.3],          // 6-11字符：30%  
            ['max_len' => 20, 'ratio' => 0.4],          // 12-20字符：40%
            ['max_len' => PHP_INT_MAX, 'ratio' => 0.5]  // 21+字符：50%
        ];

        // 处理空字符串
        if (empty($input)) {
            return $input;
        }

        $strLength = mb_strlen($input, 'UTF-8');

        // 特殊处理：1-2字符
        if ($strLength == 1) {
            return $maskChar;
        }
        if ($strLength == 2) {
            return mb_substr($input, 0, 1, 'UTF-8') . $maskChar;
        }

        // 根据字符串长度选择合适比例
        $selectedRatio = 0.3; // 默认值
        foreach ($ratios as $range) {
            if ($strLength <= $range['max_len']) {
                $selectedRatio = $range['ratio'];
                break;
            }
        }

        // 计算掩码长度（至少1个字符）
        $maskLength = max(1, floor($strLength * $selectedRatio));

        // 剩余长度平均分配给前后部分
        $remainingLength = $strLength - $maskLength;

        // 确保前后都有保留字符（除非字符串太短）
        if ($remainingLength >= 2) {
            $frontLength = floor($remainingLength / 2);
            $backLength = $remainingLength - $frontLength;
        } else {
            // 如果剩余长度不足，优先显示前面
            $frontLength = 1;
            $backLength = 0;
            $maskLength = $strLength - 1;
        }

        // 提取各部分
        $frontPart = mb_substr($input, 0, $frontLength, 'UTF-8');
        $backPart = $backLength > 0 ? mb_substr($input, $strLength - $backLength, $backLength, 'UTF-8') : '';
        $maskPart = str_repeat($maskChar, $maskLength);

        return $frontPart . $maskPart . $backPart;
    }
}
