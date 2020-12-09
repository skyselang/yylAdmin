<?php
/*
 * @Description  : index公共文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2020-11-24
 */

use think\facade\Config;
use think\facade\Request;

/**
 * 获取请求会员id
 *
 * @return integer
 */
function member_id()
{
    $member_id_key = Config::get('index.member_id_key');
    $member_id     = Request::header($member_id_key, '');

    return $member_id;
}

/**
 * 获取请求会员token
 *
 * @return string
 */
function member_token()
{
    $member_token_key = Config::get('index.member_token_key');
    $member_token     = Request::header($member_token_key, '');

    return $member_token;
}
