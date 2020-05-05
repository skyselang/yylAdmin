<?php
/*
 * @Description  : 实用工具
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-29
 */

namespace app\admin\service;

use Endroid\QrCode\QrCode;

class AdminToolService
{
    /**
     * 生成随机字符
     *
     * @param array $random_ids
     * @param integer $random_len
     * @return array
     */
    public static function randomStr($random_ids, $random_len)
    {
        $str_arr = [
            1 => '0123456789',
            2 => 'abcdefghijklmnopqrstuvwxyz',
            3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            4 => '`~!@#$%^&*()-_=+\|[]{};:' . "'" . '",.<>/?',
        ];

        $original_str = '';
        foreach ($random_ids as $v) {
            $original_str .= $str_arr[$v];
        }
        $original_str = str_shuffle($original_str);

        $random_str = '';
        $str_len = strlen($original_str) - 1;
        for ($i = 0; $i < $random_len; $i++) {
            $random_str .= $original_str[mt_rand(0, $str_len)];
        }

        $data['original_str'] = $original_str;
        $data['random_len'] = $random_len;
        $data['random_str'] = $random_str;

        return $data;
    }

    /**
     * 时间戳转换
     *
     * @param array $param
     * @return array
     */
    public static function timestamp($param)
    {
        $time = time();
        $date = date('Y-m-d H:i:s', $time);

        if ($param['from_datetime']) {
            $param['to_timestamp'] = strtotime($param['from_datetime']);
            $param['from_datetime'] = date('Y-m-d H:i:s', $param['to_timestamp']);
        } else {
            $param['to_timestamp'] = strtotime($date);
            $param['from_datetime'] = date('Y-m-d H:i:s', $time);
        }

        if ($param['from_timestamp']) {
            $param['to_datetime'] = date('Y-m-d H:i:s', $param['from_timestamp']);
            $param['from_timestamp'] = strtotime($param['to_datetime']);
        } else {
            $param['to_datetime'] = date('Y-m-d H:i:s', $time);
            $param['from_timestamp'] = strtotime($date);
        }

        return $param;
    }

    /**
     * MD5加密
     *
     * @param string $str 字符串
     * @return string
     */
    public static function md5Enc($str)
    {
        $md5_16 = substr(md5($str), 8, 16);
        $md5_32 = md5($str);

        $data['md5_16'] = $md5_16;
        $data['md5_32'] = $md5_32;

        return $data;
    }

    /**
     * 生成二维码
     *
     * @param string $qrcode_str 文本
     * @return string
     */
    public static function qrcode($qrcode_str)
    {
        $qrcode_path = '/qrcode/qrcode.png';
        $qrCode = new QrCode($qrcode_str);
        $qrCode->writeFile('.' . $qrcode_path);

        $qrcode_url = file_url($qrcode_path);
        $data['qrcode_url'] = $qrcode_url . '?r=' . mt_rand(100, 999);

        return $data;
    }
}
