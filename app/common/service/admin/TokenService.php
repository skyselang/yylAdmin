<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// Token
namespace app\common\service\admin;

use app\common\cache\admin\UserCache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{
    /**
     * Token配置
     *
     * @return array
     */
    public static function config()
    {
        return SettingService::info();
    }

    /**
     * Token生成
     * 
     * @param array $user 用户信息
     * 
     * @return string
     */
    public static function create($user)
    {
        $config = self::config();

        $payload = [
            'iat'  => time(),                               //签发时间
            'nbf'  => time(),                               //生效时间
            'exp'  => time() + $config['token_exp'] * 3600, //过期时间
            'data' => [
                'admin_user_id' => $user['admin_user_id'],
                'login_time'    => $user['login_time'],
                'login_ip'      => $user['login_ip'],
            ],
        ];

        $key = $config['token_key']; //密钥
        $token = JWT::encode($payload, $key, 'HS256');

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
            $key = $config['token_key'];
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $admin_user_id = $decode->data->admin_user_id;
        } catch (\Exception $e) {
            exception('账号登录状态已过期', 401);
        }

        $user = UserCache::get($admin_user_id);
        if (empty($user)) {
            exception('账号登录状态已失效', 401);
        } else {
            if ($token != $user['admin_token']) {
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
     * @return int admin_user_id
     */
    public static function adminUserId($token)
    {
        if (empty($token)) {
            return 0;
        }
        
        try {
            $config = self::config();
            $key = $config['token_key'];
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $admin_user_id = $decode->data->admin_user_id;
        } catch (\Exception $e) {
            $admin_user_id = 0;
        }

        return $admin_user_id;
    }
}
