<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-26
 */

namespace app\common\service;

use Firebase\JWT\JWT;

class TokenService
{
    /**
     * Token配置
     *
     * @return array
     */
    public static function config()
    {
        $config = SettingService::tokenInfo();

        return $config;
    }

    /**
     * Token生成
     * 
     * @param array $member 会员信息
     * 
     * @return string
     */
    public static function create($member)
    {
        $config = self::config();

        $key = $config['token_key'];                  //密钥
        $iat = time();                                //签发时间
        $nbf = time();                                //生效时间
        $exp = time() + $config['token_exp'] * 3600;  //过期时间

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
     * @return Exception
     */
    public static function verify($token)
    {
        try {
            $config = self::config();
            JWT::decode($token, $config['token_key'], array('HS256'));
        } catch (\Exception $e) {
            exception('登录已失效', 401);
        }
    }

    /**
     * Token会员id
     *
     * @param string $token token
     * 
     * @return integer member_id
     */
    public static function memberId($token)
    {
        try {
            $config = self::config();
            $decode = JWT::decode($token, $config['token_key'], array('HS256'));

            $member_id = $decode->data->member_id;
        } catch (\Exception $e) {
            $member_id = 0;
        }

        return $member_id;
    }
}
