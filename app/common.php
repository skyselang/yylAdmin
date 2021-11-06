<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 公共函数文件
use think\facade\Request;

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
 * 错误返回（调试用）
 *
 * @param string  $msg  错误提示
 * @param array   $data 错误数据
 * @param integer $code 错误码
 * 
 * @return json
 */
function error(string $msg = '操作失败', $data = [], int $code = 400)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['data'] = $data;

    print_r(json_encode($res, JSON_UNESCAPED_UNICODE));

    exit;
}

/**
 * 错误返回（抛出异常）
 *
 * @param string  $msg  异常提示
 * @param integer $code 错误码
 * 
 * @return Exception
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
    return Request::domain();
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
 * 文件序列化
 *
 * @param array $files 文件数组 [['name'=>'名称','path'=>'路径','size'=>'大小']]
 *
 * @return string
 */
function file_ser($files = [])
{
    if (empty($files)) {
        return serialize([]);
    }

    $files_arr = [];
    foreach ($files as $k => $v) {
        unset($v['status'], $v['uid'], $v['url']);
        $files_arr[] = $v;
    }

    return serialize($files_arr);
}

/**
 * 文件id
 *
 * @param array $files 文件数组 [['file_id'=>'文件id']]
 *
 * @return string
 */
function file_ids($files = [])
{
    if (empty($files)) {
        return 0;
    }

    $file_ids = array_column($files, 'file_id');
    $file_ids = implode(',', $file_ids);

    return $file_ids;
}

/**
 * 文件反序列化
 *
 * @param string $files 序列化后的文件数组
 *
 * @return array
 */
function file_unser($files)
{
    $files = unserialize($files);

    if (empty($files)) {
        return [];
    }

    foreach ($files as $k => $v) {
        $files[$k]['url'] = file_url($v['path']);
    }

    return $files;
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
    return trim($str, $char);
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
    return $char . $str . $char;
}

/**
 * 字符串合拼
 *
 * @param string  $str1   字符串1
 * @param string  $str2   字符串2
 * @param boolean $is_rep 是否去重
 *
 * @return string
 */
function str_merge($str1 = '', $str2 = '', $is_rep = true)
{
    $str1 = trim($str1, ',');
    $str2 = trim($str2, ',');
    $str = $str1 . ',' . $str2;

    if ($is_rep) {
        $arr = explode(',', $str);
        $arr = array_unique($arr);
        $str = implode(',', $arr);
    }

    return $str;
}
