<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\common\cache\system\UserCache;
use app\common\service\system\SettingService;
use app\common\service\utils\RetCodeUtils;

/**
 * 用户Token
 */
class UserTokenService
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
                'user_id' => $user['user_id'],
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
     * @return void|Exception
     */
    public static function verify($token)
    {
        if (empty($token)) {
            exception('请登录', RetCodeUtils::LOGIN_INVALID);
        }

        $config = self::config();

        try {
            $key = $config['token_key'];
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $user_id = $decode->data->user_id;
        } catch (\Exception $e) {
            exception('账号登录状态已失效', RetCodeUtils::LOGIN_INVALID);
        }

        $cache_token = UserCache::getToken($user_id);
        if (empty($cache_token)) {
            exception('账号登录状态已过期', RetCodeUtils::LOGIN_INVALID);
        } else {
            if (!$config['is_multi_login']) {
                if ($token != $cache_token) {
                    exception('账号已在另一处登录', RetCodeUtils::LOGIN_INVALID);
                }
            }

            $user = UserService::info($user_id);
            if ($user['is_disable'] == 1) {
                exception('账号已被禁用', RetCodeUtils::LOGIN_INVALID);
            }
            if ($user['is_delete'] == 1) {
                exception('账号已被删除', RetCodeUtils::LOGIN_INVALID);
            }
        }
    }

    /**
     * Token用户id
     *
     * @param string $token token
     * @param bool   $exce  未登录是否抛出异常
     * 
     * @return int user_id
     */
    public static function userId($token, $exce = false)
    {
        $user_id = 0;

        if ($token) {
            try {
                $config = self::config();
                $key = $config['token_key'];
                $decode = JWT::decode($token, new Key($key, 'HS256'));
                $user_id = $decode->data->user_id;
            } catch (\Exception $e) {
                $user_id = 0;
            }
        }

        if (empty($user_id) && $exce) {
            exception('请登录', RetCodeUtils::LOGIN_INVALID);
        }

        return $user_id;
    }
}
