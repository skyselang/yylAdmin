<?php
/*
 * @Description  : 实用工具
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-08-09
 */

namespace app\admin\service;

use Endroid\QrCode\QrCode;

class AdminToolService
{
    /**
     * 生成随机字符
     *
     * @param array   $random_ids 包含字符
     * @param integer $random_len 长度
     * 
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
        $str_len    = strlen($original_str) - 1;
        for ($i = 0; $i < $random_len; $i++) {
            $random_str .= $original_str[mt_rand(0, $str_len)];
        }

        $data['original_str'] = $original_str;
        $data['random_len']   = $random_len;
        $data['random_str']   = $random_str;

        return $data;
    }

    /**
     * 时间戳转换
     *
     * @param array $param
     * 
     * @return array
     */
    public static function timestamp($param)
    {
        $res['type']  = $param['type'];
        $type = $param['type'];

        if ($type == 'time') {
            $res['datetime']  = $param['datetime'];
            $res['timestamp'] = strtotime($param['datetime']);
        }

        if ($type == 'date') {
            $res['datetime']  = date('Y-m-d H:i:s', $param['timestamp']);
            $res['timestamp'] = $param['timestamp'];
        }

        return $res;
    }

    /**
     * MD5加密
     *
     * @param string $str 字符串
     * 
     * @return array
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
     * @param string $str 文本内容
     * 
     * @return array
     */
    public static function qrcode($str)
    {
        $file_dir = '/storage/admin/user';
        if (!file_exists('.' . $file_dir)) {
            mkdir('.' . $file_dir);
        }

        $admin_user_id = admin_user_id();

        $file_dir = '/storage/admin/user/' . $admin_user_id;
        if (!file_exists('.' . $file_dir)) {
            mkdir('.' . $file_dir);
        }
        $file_name = 'tool_qrcode.png';
        $file_path = $file_dir . '/' . $file_name;
        $QrCode = new QrCode($str);
        $QrCode->writeFile('.' . $file_path);

        $qrcode_url = file_url($file_path);
        $data['url'] = $qrcode_url . '?r=' . mt_rand(10, 99);

        return $data;
    }
}
