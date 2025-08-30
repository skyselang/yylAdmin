<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace thirdsdk;

/**
 * Http 请求类
 */
class Http
{
    /**
     * 平台
     * @var string
     */
    protected $platform = '';

    /**
     * 头部
     * @var array
     */
    protected $header = ['Content-type:application/json', 'Accept:application/json'];

    /**
     * 构造函数
     * @param string $platform 平台
     */
    public function __construct($platform)
    {
        $this->platform = $platform;
    }

    /**
     * GET 请求
     * @param string $url    请求地址
     * @param array  $header 请求头部
     * @return array
     */
    public function get($url, $header = [])
    {
        return $this->request('get', $url, [], $header);
    }

    /**
     * POST 请求
     * @param string $url    请求地址
     * @param array  $param  请求参数
     * @param array  $header 请求头部
     * @return array
     */
    public function post($url, $param = [], $header = [])
    {
        return $this->request('post', $url, $param, $header);
    }

    /**
     * 请求
     * @param string $method 请求方法
     * @param string $url    请求地址
     * @param array  $param  请求参数
     * @param array  $header 请求头部
     * @return array
     * @throws \think\Exception
     */
    public function request($method, $url, $param = [], $header = [])
    {
        $method = strtolower($method);
        $param  = json_encode($param);
        $header = array_merge($header, $this->header);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($method === 'post') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
            curl_setopt($curl, CURLOPT_POST, 1);
        }
        $res = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($res, true);
        $this->response($res);

        return $res;
    }

    /**
     * 响应拦截
     * @param array $res
     * @throws \think\Exception
     */
    public function response($res)
    {
        if ($this->platform == 'wx') {
            $errcode = $res['ret'] ?? $res['code'] ?? $res['errcode'] ?? '';
            $errdesc = $res['msg'] ?? $res['errmsg'] ?? 'wxsdk error';
        } elseif ($this->platform == 'qq') {
            $errcode = $res['ret'] ?? $res['code'] ?? $res['errcode'] ?? '';
            $errdesc = $res['msg'] ?? $res['errmsg'] ?? 'qqsdk error';
        } elseif ($this->platform == 'wb') {
            $errcode = $res['error'] ?? $res['error_code'] ?? '';
            $errdesc = $res['error_description'] ?? 'wbsdk error';
        }

        if ($errcode ?? '') {
            throw new \think\Exception($errcode . ' ' . $errdesc, 400);
        }
    }
}
