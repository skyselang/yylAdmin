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
 * 微信 SDK 移动应用
 */
class WxMobile
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
     * AppID 移动应用ID APP ID
     *
     * @var string
     */
    protected $appid;

    /**
     * AppSecret 移动应用密钥 AppSecret
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
     * @param  string $code
     *
     * @return array
     */
    public function login($code)
    {
        $access_token = $this->getAccessToken($code);

        return $this->getUserInfo($access_token['access_token'], $access_token['openid']);
    }

    /**
     * 获取 AccessToken
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
     * 获取用户信息
     *
     * @param  string $access_token
     * @param  string $openid
     *
     * @return array
     */
    public function getUserInfo($access_token, $openid)
    {
        $param = http_build_query([
            'access_token' => $access_token,
            'openid'       => $openid,
            'lang'         => 'zh_CN',
        ]);
        $url = 'https://api.weixin.qq.com/sns/userinfo?' . $param;
        return $this->http->get($url);
    }
}
