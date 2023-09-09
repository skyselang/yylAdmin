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
 * QQ SDK 网站应用
 */
class QqWebsite
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
    protected $platform = 'qq';

    /**
     * AppID 网站应用ID APP ID
     *
     * @var string
     */
    protected $appid;

    /**
     * AppSecret 网站应用密钥 APP Key
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
        $state = $state ?: md5(uniqid('qq', true));
        $param = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->appid,
            'redirect_uri'  => $redirect_uri,
            'state'         => $state,
        ]);
        $url = 'https://graph.qq.com/oauth2.0/authorize?' . $param;
        header('Location:' . $url);
    }

    /**
     * 获取用户信息
     *
     * @param  string $redirect_uri
     * @param  string $code
     *
     * @return array
     */
    public function getUserInfo($redirect_uri, $code)
    {
        $at = $this->getAccessToken($redirect_uri, $code);
        $od = $this->getOpenid($at['access_token']);

        $param = http_build_query([
            'access_token'       => $at['access_token'],
            'oauth_consumer_key' => $this->appid,
            'openid'             => $od['openid'],
            'fmt'                => 'json',
        ]);
        $url                    = 'https://graph.qq.com/user/get_user_info?' . $param;
        $userinfo               = $this->http->get($url);
        $userinfo['openid']     = $od['openid'];
        $userinfo['unionid']    = $od['unionid'] ?? '';
        $userinfo['headimgurl'] = $userinfo['figureurl_2'] ?? $userinfo['figureurl_qq_2'] ?? $userinfo['figureurl_qq_1'] ?? $userinfo['figureurl_1'] ?? $userinfo['figureurl_qq_1'] ?? '';
        $userinfo['nickname']   = $userinfo['nickname'] ?? $userinfo['nickName'] ?? '';

        return $userinfo;
    }

    /**
     * 获取 AccessToken
     *
     * @param  string $redirect_uri
     * @param  string $code
     *
     * @return array 
     */
    public function getAccessToken($redirect_uri, $code)
    {
        $param = http_build_query([
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->appid,
            'client_secret' => $this->appsecret,
            'code'          => $code,
            'redirect_uri'  => $redirect_uri,
            'fmt'           => 'json',
        ]);
        $url = 'https://graph.qq.com/oauth2.0/token?' . $param;
        return $this->http->get($url);
    }

    /**
     * 获取 OpenID
     *
     * @param  string $access_token
     *
     * @return array 
     */
    public function getOpenid($access_token)
    {
        try {
            $res = $this->getUnionid($access_token);
        } catch (\Exception $e) {
            $param = http_build_query([
                'access_token' => $access_token,
                'fmt'          => 'json',
            ]);
            $url = 'https://graph.qq.com/oauth2.0/me?' . $param;
            $res = $this->http->get($url);
        }

        return $res;
    }

    /**
     * 获取 UnionID
     *
     * @param  string $access_token
     *
     * @return array 
     */
    public function getUnionid($access_token)
    {
        $param = http_build_query([
            'access_token' => $access_token,
            'unionid'      => 1,
            'fmt'          => 'json',
        ]);
        $url = 'https://graph.qq.com/oauth2.0/me?' . $param;
        return $this->http->get($url);
    }
}
