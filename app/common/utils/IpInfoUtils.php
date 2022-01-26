<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// IP信息
namespace app\common\utils;

use think\facade\Cache;
use think\facade\Request;

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
            $url = 'http://ip.taobao.com/outGetIpInfo?ip=' . $ip . '&accessKey=alibaba-inc';
            $res = http_get($url);
            if (empty($res)) {
                $par['ip']        = $ip;
                $par['accessKey'] = 'alibaba-inc';
                $res = http_post($url, $par);
            }

            $ip_info = [
                'ip'       => $ip,
                'country'  => '',
                'province' => '',
                'city'     => '',
                'area'     => '',
                'region'   => '',
                'isp'      => '',
            ];

            if ($res) {
                if ($res['code'] == 0 && $res['data']) {
                    $data = $res['data'];

                    $country  = $data['country'];
                    $province = $data['region'];
                    $city     = $data['city'];
                    $area     = $data['area'];
                    $region   = $country . $province . $city . $area;
                    $isp      = $data['isp'];

                    $ip_info['ip']       = $ip;
                    $ip_info['country']  = $country;
                    $ip_info['province'] = $province;
                    $ip_info['city']     = $city;
                    $ip_info['region']   = $region;
                    $ip_info['area']     = $area;
                    $ip_info['isp']      = $isp;

                    $ip_ttl = 7 * 24 * 60 * 60;
                    Cache::set($ip_key, $ip_info, $ip_ttl);
                }
            }
        }

        return $ip_info;
    }
}
