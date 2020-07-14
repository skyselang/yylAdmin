<?php
/*
 * @Description  : ip信息
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 */

namespace app\admin\service;

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
        $url = 'http://ip.taobao.com/outGetIpInfo?ip=' . $ip . '&accessKey=alibaba-inc';
        $res = httpGet($url);

        $ip_info = [
            'ip'     => $ip,
            'region' => '',
            'isp'    => '',
        ];

        if ($res['code'] == 0) {
            $res_data = $res['data'];
            $region   = $res_data['country'] . $res_data['region'] . $res_data['city'];
            $region   = str_replace('X', '', $region);

            $ip_info['ip']     = $ip;
            $ip_info['region'] = $region;
            $ip_info['isp']    = $res_data['isp'];
        }

        return $ip_info;
    }
}
