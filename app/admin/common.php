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
use app\common\service\system\MenuService;
use app\common\service\system\SettingService;
use app\common\service\system\UserTokenService;

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
 * @return bool
 */
function menu_is_exist($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $url_list = MenuService::menuList();
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
 * @return bool
 */
function menu_is_disable($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $menu = MenuService::info($menu_url);
    if ($menu['is_disable'] == 1) {
        return true;
    }

    return false;
}

/**
 * 菜单是否免登
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unlogin($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unlogin_url = MenuService::unloginList();
    if (in_array($menu_url, $unlogin_url)) {
        return true;
    }

    return false;
}

/**
 * 菜单是否免权
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unauth($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unauth_url = MenuService::unauthList();
    if (in_array($menu_url, $unauth_url)) {
        return true;
    }

    return false;
}

/**
 * 菜单是否免限
 *
 * @param string $menu_url 菜单url
 *
 * @return bool
 */
function menu_is_unrate($menu_url = '')
{
    if (empty($menu_url)) {
        $menu_url = menu_url();
    }

    $unrate_url = MenuService::unrateList();
    if (in_array($menu_url, $unrate_url)) {
        return true;
    }

    return false;
}

/**
 * 用户token
 *
 * @return string
 */
function user_token()
{
    $system = SettingService::info();
    if ($system['token_type'] == 'header') {
        $user_token = Request::header($system['token_name'], '');
    } else {
        $user_token = Request::param($system['token_name'], '');
    }

    return $user_token;
}

/**
 * 用户token验证
 *
 * @param string $user_token 用户token
 *
 * @return Exception
 */
function user_token_verify($user_token = '')
{
    if (empty($user_token)) {
        $user_token = user_token();
    }

    UserTokenService::verify($user_token);
}

/**
 * 用户id
 *
 * @return int
 */
function user_id()
{
    return UserTokenService::userId(user_token());
}

/**
 * 系统超管用户id（所有权限）
 *
 * @return array
 */
function user_super_ids()
{
    return Config::get('admin.super_ids', []);
}

/**
 * 用户是否系统超管
 *
 * @param int $user_id 用户id
 * 
 * @return bool
 */
function user_is_super($user_id = 0)
{
    if (empty($user_id)) {
        return false;
    }

    $user_super_ids = user_super_ids();
    if (empty($user_super_ids)) {
        return false;
    }
    if (in_array($user_id, $user_super_ids)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 用户日志记录是否开启
 *
 * @return bool
 */
function user_log_switch()
{
    $system = SettingService::info();
    if ($system['log_switch']) {
        return true;
    } else {
        return false;
    }
}

/**
 * 系统超管用户记录隐藏条件
 * 
 * @param string $user_id_field 用户id字段
 *
 * @return array
 */
function user_hide_where($user_id_field = 'user_id')
{
    $super_hide = Config::get('admin.super_hide', false);
    if ($super_hide) {
        $user_id = user_id();
        if (!user_is_super($user_id)) {
            $user_super_ids = user_super_ids();
            if ($user_super_ids) {
                $where = [$user_id_field, 'not in', $user_super_ids];
            }
        }
    }
    return $where ?? [];
}
