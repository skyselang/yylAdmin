<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\common\cache\member\MemberCache;
use app\common\service\member\SettingService;
use app\common\service\utils\RetCodeUtils;

/**
 * 会员Token
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
                'member_id' => $member['member_id'],
            ],
        ];

        $key   = $config['token_key']; //密钥
        $token = JWT::encode($payload, $key, 'HS256');

        return $token;
    }

    /**
     * Token decode
     *
     * @param  string $token
     * @return object
     */
    public static function decode($token)
    {
        try {
            $config = self::config();
            $key    = $config['token_key'];
            $decode = JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            exception('登录状态已失效', RetCodeUtils::LOGIN_INVALID);
        }

        return $decode;
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
        $member_id   = self::memberId($token, true);
        $token_cache = MemberCache::getToken($member_id);
        if (empty($token_cache)) {
            exception('登录状态已过期', RetCodeUtils::LOGIN_INVALID);
        } else {
            $config = self::config();
            if (!$config['is_multi_login']) {
                if ($token != $token_cache) {
                    exception('账号已在另一处登录', RetCodeUtils::LOGIN_INVALID);
                }
            }

            $member = MemberService::info($member_id);
            if ($member['is_delete'] == 1) {
                exception('账号已被注销', RetCodeUtils::LOGIN_INVALID);
            }
            if ($member['is_disable'] == 1) {
                exception('账号已被禁用', RetCodeUtils::LOGIN_INVALID);
            }
        }
    }

    /**
     * Token会员id
     *
     * @param string $token token
     * @param bool   $exce  未登录是否抛出异常
     * 
     * @return int member_id
     */
    public static function memberId($token, $exce = false)
    {
        $member_id = 0;

        if ($token) {
            try {
                $decode    = self::decode($token);
                $member_id = $decode->data->member_id;
            } catch (\Exception $e) {
                $member_id = 0;
            }
        }

        if (empty($member_id) && $exce) {
            exception('请登录', RetCodeUtils::LOGIN_INVALID);
        }

        return $member_id;
    }
}
