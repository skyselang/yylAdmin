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
 * 微博 SDK 网站应用
 */
class WbWebsite
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
    protected $platform = 'wb';

    /**
     * AppID 网站应用ID App Key
     *
     * @var string
     */
    protected $appid;

    /**
     * AppSecret 网站应用密钥 App Secret
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
        $state = $state ?: md5(uniqid('wb', true));
        $param = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->appid,
            'redirect_uri'  => $redirect_uri,
            'state'         => $state,
        ]);
        $url = 'https://api.weibo.com/oauth2/authorize?' . $param;
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
        $access_token = $this->getAccessToken($redirect_uri, $code);
        $param        = http_build_query([
            'access_token' => $access_token['access_token'],
            'uid'          => $access_token['uid'],
        ]);

        $url                    = 'https://api.weibo.com/2/users/show.json?' . $param;
        $userinfo               = $this->http->get($url);
        $userinfo['openid']     = $userinfo['id'];
        $userinfo['unionid']    = $userinfo['idstr'] ?? '';
        $userinfo['nickname']   = $userinfo['screen_name'] ?? $userinfo['name'] ?? '';
        $userinfo['headimgurl'] = $userinfo['profile_image_url'] ?? $userinfo['avatar_large'] ?? '';

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
        ]);
        $url = 'https://api.weibo.com/oauth2/access_token?' . $param;
        return $this->http->post($url);
    }
}
