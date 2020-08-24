<?php
/*
 * @Description  : 验证码配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-09
 * @LastEditTime : 2020-08-24
 */

return [
    //验证码位数
    'length'   => 4,
    // 验证码字符集合
    // 2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY
    'codeSet'  => '0123456789',
    // 验证码过期时间（s)
    'expire'   => 180,
    // 是否使用中文验证码
    'useZh'    => false,
    // 是否使用算术验证码
    'math'     => false,
    // 是否使用背景图
    'useImgBg' => false,
    //验证码字符大小
    'fontSize' => 26,
    // 是否使用混淆曲线
    'useCurve' => true,
    //是否添加杂点
    'useNoise' => true,
    // 验证码字体 不设置则随机
    'fontttf'  => '',
    //背景颜色
    'bg'       => [243, 251, 254],
    // 验证码图片高度
    'imageH'   => 0,
    // 验证码图片宽度
    'imageW'   => 0,
];
