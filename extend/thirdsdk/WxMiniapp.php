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
 * 微信 SDK 小程序
 */
class WxMiniapp
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
     * @param array $code 登录凭证 code
     * 
     * @return array openid,unionid,errmsg,errcode(0请求成功)
     */
    public function login($code)
    {
        $param = http_build_query([
            'appid'      => $this->appid,
            'secret'     => $this->appsecret,
            'js_code'    => $code,
            'grant_type' => 'authorization_code',
        ]);
        $url = 'https://api.weixin.qq.com/sns/jscode2session?' . $param;
        return $this->http->get($url);
    }

    /**
     * 获取 AccessToken
     *
     * @return string access_token
     */
    public function getAccessToken()
    {
        $access_token_key = 'wxsdkMiniappAccessToken';
        $access_token     = Cache::get($access_token_key);
        if (empty($access_token)) {
            $param = http_build_query([
                'grant_type' => 'client_credential',
                'appid'      => $this->appid,
                'secret'     => $this->appsecret,
            ]);
            $url          = 'https://api.weixin.qq.com/cgi-bin/token?' . $param;
            $res          = $this->http->get($url);
            $access_token = $res['access_token'];
            Cache::set($access_token_key, $access_token, $res['expires_in']);
        }
        return $access_token;
    }

    /**
     * 获取手机号
     *
     * @param  string $code 手机号获取凭证 code
     *
     * @return string
     */
    public function getPhoneNumber($code)
    {
        $access_token = $this->getAccessToken();
        $param        = ['code' => $code];
        $url          = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $access_token;
        $res          = $this->http->post($url, $param);
        $phone        = $res['phone_info']['purePhoneNumber'] ?? '';
        return $phone;
    }
}
