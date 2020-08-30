<?php
/*
 * @Description  : ip信息
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-08-30
 */

namespace app\admin\service;

use app\cache\AdminIpInfoCache;

class AdminIpInfoService
{
    /**
     * 获取ip信息
     *
     * @param string $ip ip地址
     *
     * @return array
     */
    public static function info($ip)
    {
        $ip_info = AdminIpInfoCache::get($ip);

        if (empty($ip_info)) {
            $url = 'http://ip.taobao.com/outGetIpInfo?ip=' . $ip . '&accessKey=alibaba-inc';
            $res = http_get($url);

            $ip_info = [
                'ip'       => $ip,
                'country'  => '',
                'province' => '',
                'city'     => '',
                'region'   => '',
                'isp'      => '',
            ];

            if ($res['code'] == 0) {
                $data     = $res['data'];
                $country  = $data['country'];
                $province = $data['region'];
                $city     = $data['city'];
                if ($province == '香港' && $city == 'XX') {
                    $city = $province;
                }
                $region = $country . $province . $city;
                $region = str_replace('X', '', $region);

                $ip_info['ip']       = $ip;
                $ip_info['country']  = $country;
                $ip_info['province'] = $province;
                $ip_info['city']     = $city;
                $ip_info['region']   = $region;
                $ip_info['isp']      = $data['isp'];

                AdminIpInfoCache::set($ip, $ip_info);
            }
        }

        return $ip_info;
    }
}
