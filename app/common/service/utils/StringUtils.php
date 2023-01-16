<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\utils;

/**
 * 字符串
 */
class StringUtils
{
    /**
     * 随机字符串
     *
     * @param int   $length    字符长度
     * @param array $character 所用字符：1数字，2小写字母，3大写字母，4标点符号
     * 
     * @return string
     */
    public static function random($length = 12, $character = [1,2,3])
    {
        $str_arr = [
            1 => '0123456789',
            2 => 'abcdefghijklmnopqrstuvwxyz',
            3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            4 => '`~!@#$%^&*()-_=+\|[]{};:' . "'" . '",.<>/?',
        ];

        $ori = '';
        foreach ($character as $v) {
            $ori .= $str_arr[$v];
        }
        $ori = str_shuffle($ori);

        $str = '';
        $str_len = strlen($ori) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $ori[mt_rand(0, $str_len)];
        }

        return $str;
    }
}
