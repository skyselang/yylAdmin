<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 实用工具
namespace app\common\service\admin;

use app\common\utils\ByteUtils;
use app\common\utils\IpInfoUtils;
use app\common\utils\ServerUtils;

class UtilsService
{
    /**
     * 随机字符串
     *
     * @param array $ids 所用字符：1数字，2小写字母，3大写字母，4特殊符号
     * @param array $len 字符串长度
     * 
     * @return array
     */
    public static function strrand($ids = [1, 2, 3], $len = 12)
    {
        $character = [
            1 => '0123456789',
            2 => 'abcdefghijklmnopqrstuvwxyz',
            3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            4 => '`~!@#$%^&*()-_=+\|[]{};:' . "'" . '",.<>/?',
        ];

        $ori = '';
        foreach ($ids as $v) {
            $ori .= $character[$v];
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
    public static function strtran($str = '')
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
    public static function timestamp($param)
    {
        $type  = $param['type'] ?: 'timestamp';
        $value = $param['value'] ?: time();
        if ($type == 'timestamp') {
            $data['datetime']  = date('Y-m-d H:i:s', $value);
            $data['timestamp'] = $value;
        } else {
            $data['datetime']  = $value;
            $data['timestamp'] = strtotime($value);
        }

        $data['type']  = $type;
        $data['value'] = $value;

        return $data;
    }

    /**
     * 字节转换
     *
     * @param array $param 类型、数值
     *
     * @return array
     */
    public static function bytetran($param)
    {
        $value = $param['value'] ?: 1024;
        $type  = $param['type'] ?: 'B';

        return ByteUtils::shift($value, $type);
    }

    /**
     * IP查询
     *
     * @param string $param ip、域名
     *
     * @return array
     */
    public static function ipinfo($ip = '')
    {
        return IpInfoUtils::info($ip);
    }

    /**
     * 服务器信息
     *
     * @return array
     */
    public static function server()
    {
        return ServerUtils::server();
    }
}
