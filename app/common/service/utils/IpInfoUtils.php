<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\utils;

use think\facade\Cache;
use think\facade\Request;
use ip2region\Ip2Region;

/**
 * IP信息
 */
class IpInfoUtils
{
    /**
     * IP信息
     *
     * @param string $ip IP地址
     *
     * @return array
     */
    public static function info($ip = '')
    {
        if (empty($ip)) {
            $ip = Request::ip();
        }

        $ip_key  = 'ip_info:' . $ip;
        $ip_info = Cache::get($ip_key);
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

            $ip_ttl = 7 * 24 * 60 * 60;
            Cache::set($ip_key, $ip_info, $ip_ttl);
        }

        return $ip_info;
    }
}
