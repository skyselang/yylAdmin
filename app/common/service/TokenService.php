<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-15
 */

namespace app\common\service;

use think\facade\Config;
use Firebase\JWT\JWT;

class TokenService
{
    /**
     * Token生成
     * 
     * @param array $member 会员信息
     * 
     * @return string
     */
    public static function create($member)
    {
        $setting = SettingService::tokenInfo();

        $key = Config::get('index.token.key');         //密钥
        $iat = time();                                 //签发时间
        $nbf = time();                                 //生效时间
        $exp = time() + $setting['token_exp'] * 3600;  //过期时间

        $data = [
            'member_id'  => $member['member_id'],
            'login_time' => $member['login_time'],
            'login_ip'   => $member['login_ip'],
        ];

        $payload = [
            'iat'  => $iat,
            'nbf'  => $nbf,
            'exp'  => $exp,
            'data' => $data,
        ];

        $token = JWT::encode($payload, $key);

        return $token;
    }

    /**
     * Token验证
     *
     * @param string $token token
     * 
     * @return json
     */
    public static function verify($token)
    {
        try {
            $key = Config::get('index.token.key');
            JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e) {
            exception('登录状态已失效', 401);
        }
    }

    /**
     * Token会员id
     *
     * @param string $token token
     * 
     * @return integer
     */
    public static function memberId($token)
    {
        try {
            $key    = Config::get('index.token.key');
            $decode = JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e) {
            exception('登录状态已失效', 401);
        }

        $member_id = $decode->data->member_id;

        return $member_id;
    }
}
