<?php
/*
 * @Description  : 公共文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 */

use think\facade\Config;

/**
 * 成功返回
 *
 * @param array   $data 返回数据
 * @param string  $msg  成功码
 * @param integer $code 成功提示
 * @return json
 */
function success($data = null, $msg = '操作成功', $code = 200)
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
 * @return json
 */
function error($msg = '操作失败', $err = null, $code = 400)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['err']  = $err;

    print_r(json_encode($res));

    exit;
}

/**
 * 服务器地址
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
 *
 * @param string $file_path 文件路径
 * @return string
 */
function file_url($file_path = '')
{
    if (empty($file_path)) {
        return '';
    }

    if (strstr($file_path, 'http')) {
        return $file_path;
    }

    $server_url = server_url();

    if (stripos($file_path, '/') == 0) {
        $res = $server_url . $file_path;
    } else {
        $res = $server_url . '/' . $file_path;
    }

    return $res;
}

/**
 * 是否超级管理员
 *
 * @param integer $admin_user_id 用户id
 * @return boolean
 */
function super_admin($admin_user_id = 0)
{
    if (empty($admin_user_id)) {
        return false;
    }

    $super_admin = Config::get('admin.super_admin', []);
    if (empty($super_admin)) {
        return false;
    }

    if (in_array($admin_user_id, $super_admin)) {
        return true;
    } else {
        return false;
    }
}
