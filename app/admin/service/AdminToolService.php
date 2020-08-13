<?php
/*
 * @Description  : 实用工具
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-08-13
 */

namespace app\admin\service;

use Endroid\QrCode\QrCode;

class AdminToolService
{
    /**
     * 字符串
     *
     * @param string $str 字符串
     *
     * @return array
     */
    public static function string($str)
    {
        $len = mb_strlen($str, 'utf-8');
        $rev = '';
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
     * 随机字符串
     *
     * @param array   $ids 包含字符
     * @param integer $len 字符长度
     * 
     * @return array
     */
    public static function strran($ids, $len)
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
        $str_len    = strlen($ori) - 1;
        for ($i = 0; $i < $len; $i++) {
            $str .= $ori[mt_rand(0, $str_len)];
        }

        $data['ori'] = $ori;
        $data['len'] = $len;
        $data['str'] = $str;

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
