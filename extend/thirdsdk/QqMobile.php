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
 * QQ SDK 移动应用
 */
class QqMobile
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
     * AppID 移动应用ID APP ID
     *
     * @var string
     */
    protected $appid;

    /**
     * AppSecret 移动应用密钥 APP Key
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
     * @param  string $access_token
     * @param  string $openid
     *
     * @return array
     */
    public function login($access_token, $openid)
    {
        return $this->getUserInfo($access_token, $openid);
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
            'access_token'       => $access_token,
            'oauth_consumer_key' => $this->appid,
            'openid'             => $openid,
            'fmt'                => 'json',
        ]);
        $url = 'https://graph.qq.com/user/get_user_info?' . $param;
        $userinfo = $this->http->get($url);
        $userinfo['openid'] = $openid;
        try {
            $od = $this->getUnionid($access_token);
        } catch (\Exception $e) {
        }
        $userinfo['unionid']    = $od['unionid'] ?? '';
        $userinfo['headimgurl'] = $userinfo['figureurl_qq_1'] ?? $userinfo['figureurl'] ?? $userinfo['figureurl_1'] ?? $userinfo['figureurl_2'] ?? '';
        $userinfo['nickname']   = $userinfo['nickname'] ?? $userinfo['nickName'] ?? '';

        return $userinfo;
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
