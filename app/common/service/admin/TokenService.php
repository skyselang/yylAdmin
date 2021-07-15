<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-07-14
 */

namespace app\common\service\admin;

use app\common\cache\admin\UserCache;
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
     * @param array $admin_user 用户信息
     * 
     * @return string
     */
    public static function create($admin_user)
    {
        $config = self::config();

        $key = $config['token_key'];                  //密钥
        $iat = time();                                //签发时间
        $nbf = time();                                //生效时间
        $exp = time() + $config['token_exp'] * 3600;  //过期时间

        $data = [
            'admin_user_id' => $admin_user['admin_user_id'],
            'login_time'    => $admin_user['login_time'],
            'login_ip'      => $admin_user['login_ip'],
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
            $decode = JWT::decode($token, $config['token_key'], array('HS256'));

            $admin_user_id = $decode->data->admin_user_id;
        } catch (\Exception $e) {
            exception('账号登录状态已过期', 401);
        }

        $admin_user = UserCache::get($admin_user_id);

        if (empty($admin_user)) {
            exception('账号登录状态已失效', 401);
        } else {
            if ($token != $admin_user['admin_token']) {
                exception('账号已在另一处登录', 401);
            } else {
                if ($admin_user['is_disable'] == 1) {
                    exception('账号已被禁用', 401);
                }
                if ($admin_user['is_delete'] == 1) {
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
     * @return integer admin_user_id
     */
    public static function adminUserId($token)
    {
        try {
            $config = self::config();
            $decode = JWT::decode($token, $config['token_key'], array('HS256'));

            $admin_user_id = $decode->data->admin_user_id;
        } catch (\Exception $e) {
            $admin_user_id = 0;
        }

        return $admin_user_id;
    }
}
