<?php
/*
 * @Description  : IP信息
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-12-03
 */

namespace app\common\service;

use app\common\cache\IpInfoCache;

class IpInfoService
{
    /**
     * 获取IP信息
     *
     * @param string $ip IP地址
     *
     * @return array
     */
    public static function info($ip)
    {
        $ipinfo = IpInfoCache::get($ip);

        if (empty($ipinfo)) {
            $url = 'http://ip.taobao.com/outGetIpInfo?ip=' . $ip . '&accessKey=alibaba-inc';
            $res = http_get($url);

            $ipinfo = [
                'ip'       => $ip,
                'country'  => '',
                'province' => '',
                'city'     => '',
                'area'     => '',
                'region'   => '',
                'isp'      => '',
            ];

            if ($res['code'] == 0 && $res['data']) {
                $data = $res['data'];

                $country  = $data['country'];
                $province = $data['region'];
                $city     = $data['city'];
                $area     = $data['area'];
                $region   = $country . $province . $city . $area;
                $isp      = $data['isp'];

                $ipinfo['ip']       = $ip;
                $ipinfo['country']  = $country;
                $ipinfo['province'] = $province;
                $ipinfo['city']     = $city;
                $ipinfo['region']   = $region;
                $ipinfo['area']     = $area;
                $ipinfo['isp']      = $isp;

                IpInfoCache::set($ip, $ipinfo);
            }
        }

        return $ipinfo;
    }
}
