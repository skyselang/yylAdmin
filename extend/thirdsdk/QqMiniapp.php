<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace thirdsdk;

use think\facade\Cache;

/**
 * QQ SDK 小程序
 */
class QqMiniapp
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
     * AppID 小程序ID AppID
     *
     * @var string
     */
    protected $appid;

    /**
     * AppSecret 小程序密钥 AppSecret
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
     * @param  string $js_code
     *
     * @return array openid,unionid,errmsg,errcode(0请求成功)
     */
    public function login($js_code)
    {
        $param = http_build_query([
            'appid'      => $this->appid,
            'secret'     => $this->appsecret,
            'js_code'    => $js_code,
            'grant_type' => 'authorization_code',
        ]);
        $url = 'https://api.q.qq.com/sns/jscode2session?' . $param;
        return $this->http->get($url);
    }

    /**
     * 获取 AccessToken
     *
     * @return string access_token
     */
    public function getAccessToken()
    {
        $access_token_key = 'qqsdkMiniappAccessToken';
        $access_token     = Cache::get($access_token_key);
        if (empty($access_token)) {
            $param = http_build_query([
                'grant_type' => 'client_credential',
                'appid'      => $this->appid,
                'secret'     => $this->appsecret,
            ]);
            $url          = 'https://api.q.qq.com/api/getToken?' . $param;
            $res          = $this->http->get($url);
            $access_token = $res['access_token'];
            Cache::set($access_token_key, $access_token, $res['expires_in']);
        }
        return $access_token;
    }
}
