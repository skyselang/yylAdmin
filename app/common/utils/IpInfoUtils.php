<?php
/*
 * @Description  : IP信息
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2021-05-07
 */

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

        $key = 'IpInfo:' . $ip;

        $ip_info = Cache::get($key);

        if (empty($ip_info)) {
            $url = 'http://ip.taobao.com/outGetIpInfo?ip=' . $ip . '&accessKey=alibaba-inc';
            $res = http_get($url);

            if (empty($res)) {
                $par = [
                    'ip' => $ip,
                    'accessKey' => 'alibaba-inc'
                ];
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

                    $ttl = 7 * 24 * 60 * 60;

                    Cache::set($key, $ip_info, $ttl);
                }
            }
        }

        return $ip_info;
    }
}
