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
use app\common\service\member\SettingService;
use app\common\utils\ReturnCodeUtils;

/**
 * 会员Token
 */
class TokenService
{
    /**
     * 会员Token配置
     */
    public static function config()
    {
        $config = SettingService::info();
        $config['token_alg'] = 'HS256';

        return $config;
    }

    /**
     * 会员Token生成
     * @param array $member 会员信息
     */
    public static function create($member)
    {
        $config = self::config();

        $payload = [
            'iat'  => time(),                           //签发时间
            'nbf'  => time(),                           //生效时间
            'exp'  => time() + $config['token_exps'],   //过期时间
            'data' => [
                'member_id'  => $member['member_id'],
                'login_time' => $member['login_time'],
            ],
        ];

        return JWT::encode($payload, $config['token_key'], $config['token_alg']);
    }

    /**
     * 会员Token decode
     * @param string $token
     */
    public static function decode($token)
    {
        try {
            $config = self::config();
            $decode = JWT::decode($token, new Key($config['token_key'], $config['token_alg']));
        } catch (\Exception $e) {
            exception(lang('账号登录状态已失效'), ReturnCodeUtils::LOGIN_INVALID);
        }

        return $decode;
    }

    /**
     * 会员Token验证
     * @param string $token token
     */
    public static function verify($token)
    {
        if (empty($token)) {
            exception(lang('请登录'), ReturnCodeUtils::LOGIN_INVALID);
        }

        try {
            $decode    = self::decode($token);
            $member_id = $decode->data->member_id;
        } catch (\Exception $e) {
            exception(lang('账号登录状态已失效'), ReturnCodeUtils::LOGIN_INVALID);
        }

        $member = MemberService::info($member_id);
        if ($member['is_delete'] == 1) {
            exception(lang('账号已被注销'), ReturnCodeUtils::LOGIN_INVALID);
        }
        if ($member['is_disable'] == 1) {
            exception(lang('账号已被禁用'), ReturnCodeUtils::LOGIN_INVALID);
        }
        if (($member['pwd_time'] ?? '') && $decode->data->login_time < $member['pwd_time']) {
            exception(lang('账号密码已修改'), ReturnCodeUtils::LOGIN_INVALID);
        }

        $config = self::config();
        if ($config['is_multi_login'] == 0) {
            if ($decode->data->login_time != $member['login_time']) {
                exception(lang('账号已在另一处登录'), ReturnCodeUtils::LOGIN_INVALID);
            }
        }
    }

    /**
     * 会员Token会员id
     * @param string $token token
     * @param bool   $exce  是否抛出异常
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
            exception(lang('请登录'), ReturnCodeUtils::LOGIN_INVALID);
        }

        return $member_id;
    }
}
