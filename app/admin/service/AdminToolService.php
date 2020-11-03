<?php
/*
 * @Description  : 实用工具
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-10-29
 */

namespace app\admin\service;

use Endroid\QrCode\QrCode;

class AdminToolService
{
    /**
     * 随机字符串
     *
     * @param array   $ids 包含字符
     * @param integer $len 字符长度
     * 
     * @return array
     */
    public static function strRand($ids = [1, 2, 3], $len = 12)
    {
        $str_arr = [
            1 => '0123456789',
            2 => 'abcdefghijklmnopqrstuvwxyz',
            3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            4 => '`~!@#$%^&*()-_=+\|[]{};:' . "'" . '",.<>/?',
        ];

        $ori = '';
        foreach ($ids as $v) {
            $ori .= $str_arr[$v];
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
    public static function strTran($str = '')
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
    public static function timeTran($param)
    {
        $type  = $param['type'] ?: 'timestamp';
        $value = $param['value'] ?: time();

        $data['type']  = $type;
        $data['value'] = $value;

        if ($type == 'timestamp') {
            $data['datetime']  = date('Y-m-d H:i:s', $value);
            $data['timestamp'] = $value;
        } else {
            $data['datetime']  = $value;
            $data['timestamp'] = strtotime($value);
        }

        return $data;
    }

    /**
     * 生成二维码
     *
     * @param string $str 文本内容
     * 
     * @return array
     */
    public static function qrcode($str = '')
    {
        if (empty($str)) {
            $str = 'https://gitee.com/skyselang/yylAdmin';
        }

        $admin_user_id = admin_user_id();

        $file_dir = '/storage/admin/user/' . $admin_user_id;
        if (!file_exists('.' . $file_dir)) {
            mkdir('.' . $file_dir, 0777, true);
        }

        $file_name = 'tool_qrcode.png';
        $file_path = $file_dir . '/' . $file_name;
        $QrCode = new QrCode($str);
        $QrCode->writeFile('.' . $file_path);

        $qrcode_url = file_url($file_path);

        $data['str'] = $str;
        $data['url'] = $qrcode_url . '?r=' . mt_rand(1, 99);

        return $data;
    }

    /**
     * 字节转换
     *
     * @param array $param 类型、数值
     *
     * @return array
     */
    public static function byteTran($param)
    {
        $type  = $param['type'] ?: 'b';
        $value = $param['value'] ?: 0;

        $hex_b = 8;
        $hex_B = 1024;

        $data['type']  = $type;
        $data['value'] = $value;

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

        return $data;
    }

    /**
     * IP查询
     *
     * @param array $param ip、域名
     *
     * @return array
     */
    public static function ipQuery($ip = '')
    {
        $ip_info = AdminIpInfoService::info($ip);

        return $ip_info;
    }
}
