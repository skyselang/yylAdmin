<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// api公共函数文件
use think\facade\Config;
use think\facade\Request;
use app\common\service\member\ApiService;
use app\common\service\member\SettingService;

/**
 * 接口url获取
 * 应用/控制器/操作 
 * @return string eg：api/Index/index
 */
function api_url()
{
    return app('http')->getName() . '/' . Request::pathinfo();
}

/**
 * 接口是否存在
 * @param string $api_url 接口url
 * @return bool
 */
function api_is_exist($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $api_list = ApiService::apiList();
    if (in_array($api_url, $api_list)) {
        return true;
    }

    return false;
}

/**
 * 接口是否已禁用
 * @param string $api_url 接口url
 * @return bool
 */
function api_is_disable($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $api = ApiService::info($api_url);
    if ($api['is_disable'] == 1) {
        return true;
    }

    return false;
}

/**
 * 接口是否免登
 * @param string $api_url 接口url
 * @return bool
 */
function api_is_unlogin($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unlogin_url = ApiService::unloginList();
    if (in_array($api_url, $unlogin_url)) {
        return true;
    }

    return false;
}

/**
 * 接口是否免权
 * @param string $api_url 接口url
 * @return bool
 */
function api_is_unauth($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unauth_url = ApiService::unauthList();
    if (in_array($api_url, $unauth_url)) {
        return true;
    }

    return false;
}

/**
 * 接口是否免限
 * @param string $api_url 接口url
 * @return bool
 */
function api_is_unrate($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unrate_url = ApiService::unrateList();
    if (in_array($api_url, $unrate_url)) {
        return true;
    }

    return false;
}

/**
 * 会员平台
 * @return int 请求参数或头部携带的平台代码
 */
function member_platform()
{
    $field = 'platform';
    $admin = Config::get('admin');
    if ($admin['token_type'] == 'header') {
        $platform = Request::header($field, '');
    } else {
        $platform = Request::param($field, '');
    }
    if (empty($platform)) {
        $platform = Request::param($field, '');
    }
    if (empty($platform)) {
        $platform = Request::header($field, '');
    }
    if (empty($platform)) {
        $platform = SettingService::platform(member_application());
    }

    return $platform;
}

/**
 * 会员应用
 * @return int 请求参数或头部携带的应用代码
 */
function member_application()
{
    $field = 'application';
    $admin = Config::get('admin');
    if ($admin['token_type'] == 'header') {
        $application = Request::header($field, '');
    } else {
        $application = Request::param($field, '');
    }
    if (empty($application)) {
        $application = Request::param($field, SettingService::APP_UNKNOWN);
        if (empty($application)) {
            $application = Request::header($field, SettingService::APP_UNKNOWN);
        }
    }

    return $application;
}
