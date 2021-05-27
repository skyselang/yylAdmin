<?php
/*
 * @Description  : admin公共函数文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2021-05-26
 */

use think\facade\Config;
use think\facade\Request;
use app\common\service\AdminMenuService;
use app\common\service\AdminSettingService;
use app\common\service\AdminTokenService;

/**
 * 菜单url获取
 * 应用/控制器/操作 
 * eg：admin/Index/index
 *
 * @return string
 */
function menu_url()
{
    $menu_url = app('http')->getName() . '/' . Request::pathinfo();

    return $menu_url;
}

/**
 * 菜单是否存在
 *
 * @param string $menu_url 菜单url
 *
 * @return boolean
 */
function menu_is_exist($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $url_list = AdminMenuService::urlList();
    if (in_array($menu_url, $url_list)) {
        return true;
    }

    return false;
}

/**
 * 菜单是否已禁用
 *
 * @param string $menu_url 菜单url
 *
 * @return boolean
 */
function menu_is_disable($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $menu_info = AdminMenuService::info($menu_url);

    if ($menu_info['is_disable'] == 1) {
        return true;
    }

    return false;
}

/**
 * 菜单是否无需权限
 *
 * @param string $menu_url 菜单url
 *
 * @return boolean
 */
function menu_is_unauth($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unauthlist = AdminMenuService::unauthList();
    if (in_array($menu_url, $unauthlist)) {
        return true;
    }

    return false;
}

/**
 * 菜单是否无需登录
 *
 * @param string $menu_url 菜单url
 *
 * @return boolean
 */
function menu_is_unlogin($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unloginlist = AdminMenuService::unloginList();
    if (in_array($menu_url, $unloginlist)) {
        return true;
    }

    return false;
}



/**
 * 用户token是否已设置
 *
 * @return bool
 */
function admin_token_has()
{
    $token_info = AdminSettingService::tokenInfo();

    $token_name = $token_info['token_name'];
    $token_name = strtolower($token_name);
    $header     = Request::header();

    if (isset($header[$token_name])) {
        return true;
    }

    return false;
}

/**
 * 用户token获取
 *
 * @return string
 */
function admin_token()
{
    $token_info = AdminSettingService::tokenInfo();

    $token_name  = $token_info['token_name'];
    $admin_token = Request::header($token_name, '');

    return $admin_token;
}

/**
 * 用户id获取
 *
 * @return integer
 */
function admin_user_id()
{
    $admin_token   = admin_token();
    $admin_user_id = AdminTokenService::adminUserId($admin_token);

    return $admin_user_id;
}

/**
 * 用户是否超管
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

/**
 * 日志记录是否开启
 *
 * @return bool
 */
function admin_log_switch()
{
    $log_info = AdminSettingService::logInfo();
    if ($log_info['log_switch']) {
        return true;
    } else {
        return false;
    }
}
