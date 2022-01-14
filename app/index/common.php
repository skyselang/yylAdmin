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

    $api_info = ApiService::info($api_url);
    if ($api_info['is_disable'] == 1) {
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

    $unloginlist = ApiService::unloginList();
    if (in_array($api_url, $unloginlist)) {
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

    $unratelist = ApiService::unrateList();
    if (in_array($api_url, $unratelist)) {
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
    $tokne_info = SettingService::tokenInfo();
    $token_key  = $tokne_info['token_name'];
    $token_key  = strtolower($token_key);

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
    $tokne_info   = SettingService::tokenInfo();
    $token_key    = $tokne_info['token_name'];
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
    $log_info = SettingService::logInfo();
    if ($log_info['log_switch']) {
        return true;
    } else {
        return false;
    }
}
