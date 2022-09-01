<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\member\MemberCache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Token
 */
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
     * @param array $member 会员信息
     * 
     * @return string
     */
    public static function create($member)
    {
        $config = self::config();

        $payload = [
            'iat'  => time(),                               //签发时间
            'nbf'  => time(),                               //生效时间
            'exp'  => time() + $config['token_exp'] * 3600, //过期时间
            'data' => [
                'member_id'  => $member['member_id'],
                'login_time' => $member['login_time'],
                'login_ip'   => $member['login_ip'],
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
        if (empty($token)) {
            exception('请登录', 401);
        }

        $config = self::config();

        try {
            $key = $config['token_key'];
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $member_id = $decode->data->member_id;
        } catch (\Exception $e) {
            exception('账号登录状态已过期'.$e->getMessage(), 401);
        }

        $member = MemberCache::get($member_id ?? 0);
        if (empty($member)) {
            exception('账号登录状态已失效', 401);
        } else {
            if (!$config['is_multi_login']) {
                if ($token != $member['api_token']) {
                    exception('账号已在另一处登录', 401);
                }
            }

            if ($member['is_disable'] == 1) {
                exception('账号已被禁用', 401);
            }
            if ($member['is_delete'] == 1) {
                exception('账号已被删除', 401);
            }
        }
    }

    /**
     * Token会员id
     *
     * @param string $token token
     * 
     * @return int member_id
     */
    public static function memberId($token)
    {
        if (empty($token)) {
            return 0;
        }

        try {
            $config = self::config();
            $key = $config['token_key'];
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $member_id = $decode->data->member_id;
        } catch (\Exception $e) {
            $member_id = 0;
        }

        return $member_id;
    }
}
