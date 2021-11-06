<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// admin公共函数文件
use think\facade\Config;
use think\facade\Request;
use app\common\service\admin\MenuService;
use app\common\service\admin\SettingService;
use app\common\service\admin\TokenService;

/**
 * 菜单url获取
 * 应用/控制器/操作 
 * 
 * @return string eg：admin/Index/index
 */
function menu_url()
{
    return app('http')->getName() . '/' . Request::pathinfo();
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

    $url_list = MenuService::urlList();
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

    $menu_info = MenuService::info($menu_url);
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

    $unauthlist = MenuService::unauthList();
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

    $unloginlist = MenuService::unloginList();
    if (in_array($menu_url, $unloginlist)) {
        return true;
    }

    return false;
}



/**
 * 用户token是否已设置
 *
 * @return boolean
 */
function admin_token_has()
{
    $token_info = SettingService::tokenInfo();

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
    $token_info = SettingService::tokenInfo();
    $token_name = $token_info['token_name'];

    return Request::header($token_name, '');
}

/**
 * 用户id获取
 *
 * @return integer
 */
function admin_user_id()
{
    $admin_token = admin_token();

    return TokenService::adminUserId($admin_token);
}

/**
 * 用户是否超管
 *
 * @param integer $admin_user_id 用户id
 * 
 * @return boolean
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
 * @return boolean
 */
function admin_log_switch()
{
    $log_info = SettingService::logInfo();
    if ($log_info['log_switch']) {
        return true;
    } else {
        return false;
    }
}
