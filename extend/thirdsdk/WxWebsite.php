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
 * 微信 SDK 网站应用
 */
class WxWebsite
{
    /**
     * 请求类
     *
     * @var Http
     */
    protected $http = null;

    /**
     * 平台
     *
     * @var string
     */
    protected $platform = 'wx';

    /**
     * AppID 网站应用ID AppID
     *
     * @var string
     */
    protected $appid;

    /**
     * AppSecret 网站应用密钥 AppSecret
     *
     * @var string
     */
    protected $appsecret;

    /**
     * 构造函数
     *
     * @param  string $appid     
     * @param  string $appsecret 
     */
    public function __construct($appid, $appsecret)
    {
        $this->http      = new Http($this->platform);
        $this->appid     = $appid;
        $this->appsecret = $appsecret;
    }

    /**
     * 登录
     *
     * @param  string $redirect_uri
     * @param  string $state
     *
     * @return void
     */
    public function login($redirect_uri, $state = '')
    {
        $state = $state ?: md5(uniqid('wx', true));
        $param = http_build_query([
            'appid'         => $this->appid,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => 'snsapi_login',
            'state'         => $state,
        ]);
        $url = 'https://open.weixin.qq.com/connect/qrconnect?' . $param;
        header('Location:' . $url);
    }

    /**
     * 获取 Access Token
     *
     * @param  string $code
     *
     * @return array 
     */
    public function getAccessToken($code)
    {
        $param = http_build_query([
            'appid'      => $this->appid,
            'secret'     => $this->appsecret,
            'code'       => $code,
            'grant_type' => 'authorization_code',
        ]);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . $param;
        return $this->http->get($url);
    }

    /**
     * 获取用户个人信息
     *
     * @param  string $code
     *
     * @return array
     */
    public function getUserinfo($code)
    {
        $access_token = $this->getAccessToken($code);
        $param        = http_build_query([
            'access_token' => $access_token['access_token'],
            'openid'       => $access_token['openid'],
            'lang'         => 'zh_CN',
        ]);
        $url = 'https://api.weixin.qq.com/sns/userinfo?' . $param;
        return $this->http->get($url);
    }
}
