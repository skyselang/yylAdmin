<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-03-10
 */

namespace app\admin\service;

use think\facade\Config;
use app\common\cache\UserCache;
use Firebase\JWT\JWT;

class TokenService
{
    /**
     * Token生成
     * 
     * @param array $user 用户信息
     * 
     * @return string
     */
    public static function create($user = [])
    {
        $setting = SettingService::setting();
        $token   = $setting['token'];

        $key = Config::get('index.token.key');  //密钥
        $iss = $token['iss'];                   //签发者
        $iat = time();                          //签发时间
        $nbf = time();                          //生效时间
        $exp = time() + $token['exp'] * 3600;   //过期时间

        $data = [
            'user_id'    => $user['user_id'],
            'login_time' => $user['login_time'],
            'login_ip'   => $user['login_ip'],
        ];

        $payload = [
            'iss'  => $iss,
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
            $key    = Config::get('index.token.key');
            $decode = JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e) {
            exception('登录状态错误', 401);
        }

        $user_id = $decode->data->user_id;
        $user    = UserCache::get($user_id);

        if (empty($user)) {
            exception('账号登录状态失效', 401);
        } else {
            if ($token != $user['user_token']) {
                exception('账号已在另一处登录', 401);
            } else {
                if ($user['is_disable'] == 1) {
                    exception('账号已被禁用', 401);
                }
                if ($user['is_delete'] == 1) {
                    exception('账号已被删除', 401);
                }
            }
        }
    }

    /**
     * Token用户id
     *
     * @param string $token token
     * 
     * @return integer
     */
    public static function userId($token)
    {
        try {
            $key    = Config::get('index.token.key');
            $decode = JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e) {
            exception('用户登录状态已过期', 401);
        }

        $user_id = $decode->data->user_id;

        return $user_id;
    }
}
