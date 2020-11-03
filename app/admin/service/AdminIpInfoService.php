<?php
/*
 * @Description  : ip信息
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 * @LastEditTime : 2020-10-24
 */

namespace app\admin\service;

use app\common\cache\AdminIpInfoCache;

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

                $ip_info['ip']       = $ip;
                $ip_info['country']  = $country;
                $ip_info['province'] = $province;
                $ip_info['city']     = $city;
                $ip_info['region']   = $region;
                $ip_info['area']     = $area;
                $ip_info['isp']      = $isp;

                AdminIpInfoCache::set($ip, $ip_info);
            }
        }

        return $ip_info;
    }
}
