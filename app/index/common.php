<?php
/*
 * @Description  : index公共函数文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-05-27
 */

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
    $api_url = app('http')->getName() . '/' . Request::pathinfo();

    return $api_url;
}

/**
 * 接口是否存在
 *
 * @param string $api_url 接口url
 *
 * @return boolean
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
 * @return boolean
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
 * @return boolean
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
 * 会员token是否已设置
 *
 * @return bool
 */
function member_token_has()
{
    $tokne_info = SettingService::tokenInfo();
    $token_key  = $tokne_info['token_name'];
    $token_key  = strtolower($token_key);
    $header     = Request::header();

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
 * @return integer
 */
function member_id()
{
    $member_token = member_token();
    $member_id    = TokenService::memberId($member_token);

    return $member_id;
}

/**
 * 日志记录是否开启
 *
 * @return bool
 */
function index_log_switch()
{
    $log_info = SettingService::logInfo();
    if ($log_info['log_switch']) {
        return true;
    } else {
        return false;
    }
}
