<?php
/*
 * @Description  : admin公共文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2021-05-06
 */

use think\facade\Config;
use think\facade\Request;

/**
 * 获取请求用户id
 *
 * @return integer
 */
function admin_user_id()
{
    $user_id_key   = Config::get('admin.user_id_key');
    $admin_user_id = Request::header($user_id_key, '');

    return $admin_user_id;
}

/**
 * 获取请求用户token
 *
 * @return string
 */
function admin_token()
{
    $token_key   = Config::get('admin.token_key');
    $admin_token = Request::header($token_key, '');

    return $admin_token;
}

/**
 * 判断用户是否超管
 *
 * @param integer $admin_user_id 用户id
 * 
 * @return bool
 */
function admin_is_super($admin_user_id = 0)
{
    if (empty($admin_user_id)) {
        return false;
    }

    $admin_super_ids = Config::get('admin.super_ids', []);
    if (empty($admin_super_ids)) {
        return false;
    }

    if (in_array($admin_user_id, $admin_super_ids)) {
        return true;
    } else {
        return false;
    }
}
