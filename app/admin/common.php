<?php
/*
 * @Description  : admin公共文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2021-03-23
 */

use think\facade\Config;
use think\facade\Request;

/**
 * 获取请求管理员id
 *
 * @return integer
 */
function admin_admin_id()
{
    $admin_admin_id_key = Config::get('admin.admin_admin_id_key');
    $admin_admin_id     = Request::header($admin_admin_id_key, '');

    return $admin_admin_id;
}

/**
 * 获取请求管理员token
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
 * 判断管理员是否系统管理员
 *
 * @param integer $admin_admin_id 管理员id
 * 
 * @return bool
 */
function admin_is_system($admin_admin_id = 0)
{
    if (empty($admin_admin_id)) {
        return false;
    }

    $sys_admin_ids = Config::get('admin.sys_admin_ids', []);
    if (empty($sys_admin_ids)) {
        return false;
    }

    if (in_array($admin_admin_id, $sys_admin_ids)) {
        return true;
    } else {
        return false;
    }
}
