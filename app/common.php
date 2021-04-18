<?php
/*
 * @Description  : 公共函数文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2021-04-13
 */

use think\facade\Config;
use think\facade\Request;
use app\common\service\TokenService;

/**
 * 成功返回
 *
 * @param array   $data 成功数据
 * @param string  $msg  成功提示
 * @param integer $code 成功码
 * 
 * @return json
 */
function success($data = [], string $msg = '操作成功', int $code = 200)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['data'] = $data;

    return json($res);
}

/**
 * 错误返回
 *
 * @param string  $msg  错误提示
 * @param array   $err  错误数据
 * @param integer $code 错误码
 * 
 * @return json
 */
function error(string $msg = '操作失败', $err = [], int $code = 400)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['err']  = $err;

    print_r(json_encode($res, JSON_UNESCAPED_UNICODE));

    exit;
}

/**
 * 抛出异常
 *
 * @param string  $msg  异常提示
 * @param integer $code 错误码
 * 
 * @return json
 */
function exception(string $msg = '操作失败', int $code = 400)
{
    throw new \think\Exception($msg, $code);
}

/**
 * 服务器地址
 * 协议和域名
 *
 * @return string
 */
function server_url()
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        $http = 'https://';
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        $http = 'https://';
    } else {
        $http = 'http://';
    }

    $host = $_SERVER['HTTP_HOST'];
    $res  = $http . $host;

    return $res;
}

/**
 * 文件地址
 * 协议/域名/文件路径
 *
 * @param string $file_path 文件路径
 * 
 * @return string
 */
function file_url($file_path = '')
{
    if (empty($file_path)) {
        return '';
    }

    if (strpos($file_path, 'http') !== false) {
        return $file_path;
    }

    $server_url = server_url();

    if (stripos($file_path, '/') === 0) {
        $res = $server_url . $file_path;
    } else {
        $res = $server_url . '/' . $file_path;
    }

    return $res;
}

/**
 * 获取请求pathinfo
 * 应用/控制器/操作 
 * eg：admin/Index/index
 *
 * @return string
 */
function request_pathinfo()
{
    $request_pathinfo = app('http')->getName() . '/' . Request::pathinfo();

    return $request_pathinfo;
}

/**
 * http get 请求
 *
 * @param string $url    请求地址
 * @param array  $header 请求头部
 *
 * @return array
 */
function http_get($url, $header = [])
{
    if (empty($header)) {
        $header = [
            "Content-type:application/json;",
            "Accept:application/json"
        ];
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
}

/**
 * http post 请求
 *
 * @param string $url    请求地址
 * @param array  $param  请求参数
 * @param array  $header 请求头部
 *
 * @return array
 */
function http_post($url, $param = [], $header = [])
{
    $param = json_encode($param);

    if (empty($header)) {
        $header = [
            "Content-type:application/json;charset='utf-8'",
            "Accept:application/json"
        ];
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
}

/**
 * 获取会员id
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
 * 获取会员token
 *
 * @return string
 */
function member_token()
{
    $token_key    = Config::get('index.token_key');
    $member_token = Request::header($token_key, '');

    return $member_token;
}

/**
 * 获取当前日期时间
 * format：Y-m-d H:i:s
 *
 * @return string
 */
function datetime()
{
    return date('Y-m-d H:i:s');
}

/**
 * 去除字符串首尾字符
 * 
 * @param string $str  字符串
 * @param string $char 要去除的字符
 * 
 * @return string
 */
function str_trim($str, $char = ',')
{
    $str = trim($str, $char);

    return $str;
}

/**
 * 在字符串首尾拼接字符
 * 
 * @param string $str  字符串
 * @param string $char 要拼接的字符
 * 
 * @return string
 */
function str_join($str, $char = ',')
{
    $str = $char . $str . $char;

    return $str;
}
