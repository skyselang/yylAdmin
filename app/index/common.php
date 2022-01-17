<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// index公共函数文件
use think\facade\Request;
use app\common\service\ApiService;
use app\common\service\SettingService;
use app\common\service\TokenService;

/**
 * 接口url获取
 * 应用/控制器/操作 
 * eg：index/Index/index
 *
 * @return string
 */
function api_url()
{
    return app('http')->getName() . '/' . Request::pathinfo();
}

/**
 * 接口是否存在
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_exist($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $url_list = ApiService::urlList();
    if (in_array($api_url, $url_list)) {
        return true;
    }

    return false;
}

/**
 * 接口是否已禁用
 *
 * @param string $api_url 接口url
 *
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
 * 接口是否无需登录
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_unlogin($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unlogin_url = ApiService::unloginUrl();
    if (in_array($api_url, $unlogin_url)) {
        return true;
    }

    return false;
}

/**
 * 接口是否无需限率
 *
 * @param string $api_url 接口url
 *
 * @return bool
 */
function api_is_unrate($api_url = '')
{
    if (empty($api_url)) {
        $api_url = api_url();
    }

    $unrate_url = ApiService::unrateUrl();
    if (in_array($api_url, $unrate_url)) {
        return true;
    }

    return false;
}

/**
 * 会员token是否已设置
 *
 * @return bool
 */
function member_token_has()
{
    $setting   = SettingService::info();
    $token_key = $setting['token_name'];
    $token_key = strtolower($token_key);

    $header = Request::header();
    if (isset($header[$token_key])) {
        return true;
    }

    return false;
}

/**
 * 会员token获取
 *
 * @return string
 */
function member_token()
{
    $setting      = SettingService::info();
    $token_key    = $setting['token_name'];
    $member_token = Request::header($token_key, '');

    return $member_token;
}

/**
 * 会员token验证
 *
 * @param string $member_token 会员token
 *
 * @return Exception
 */
function member_token_verify($member_token = '')
{
    if (empty($member_token)) {
        $member_token = member_token();
    }

    TokenService::verify($member_token);
}

/**
 * 会员id获取
 *
 * @return int
 */
function member_id()
{
    $member_token = member_token();

    return TokenService::memberId($member_token);
}

/**
 * 会员日志是否开启
 *
 * @return bool
 */
function member_log_switch()
{
    $setting = SettingService::info();
    if ($setting['log_switch']) {
        return true;
    } else {
        return false;
    }
}
