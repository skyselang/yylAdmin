<?php
/*
 * @Description  : admin公共文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2020-11-19
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
    $admin_user_id_key = Config::get('admin.admin_user_id_key');
    $admin_user_id     = Request::header($admin_user_id_key, '');

    return $admin_user_id;
}

/**
 * 获取请求用户token
 *
 * @return string
 */
function admin_token()
{
    $admin_token_key = Config::get('admin.admin_token_key');
    $admin_token     = Request::header($admin_token_key, '');

    return $admin_token;
}

/**
 * 判断用户是否系统管理员
 *
 * @param integer $admin_user_id 用户id
 * 
 * @return bool
 */
function admin_is_admin($admin_user_id = 0)
{
    if (empty($admin_user_id)) {
        return false;
    }

    $admin_ids = Config::get('admin.admin_ids', []);
    if (empty($admin_ids)) {
        return false;
    }

    if (in_array($admin_user_id, $admin_ids)) {
        return true;
    } else {
        return false;
    }
}
