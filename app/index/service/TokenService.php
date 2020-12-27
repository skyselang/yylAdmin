<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2020-12-25
 */

namespace app\index\service;

use think\facade\Config;
use app\common\cache\MemberCache;
use Firebase\JWT\JWT;

class TokenService
{
    /**
     * Token生成
     * 
     * @param array $member 会员数据
     * 
     * @return string
     */
    public static function create($member = [])
    {
        $time  = time();
        $token = Config::get('index.token', []);

        $key = $token['key'];                 //密钥
        $iss = $token['iss'];                 //签发者
        $iat = $time;                         //签发时间
        $nbf = $time;                         //生效时间
        $exp = $time + $token['exp'] * 3600;  //过期时间

        $data = [
            'member_id'  => $member['member_id'],
            'login_time' => $member['login_time'],
            'login_ip'   => $member['login_ip'],
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
     * @param string  $token     token
     * @param integer $member_id 会员id
     * 
     * @return json
     */
    public static function verify($token, $member_id = 0)
    {
        try {
            $key    = Config::get('index.token.key');
            $decode = JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e) {
            exception('账号登录状态已过期', 401);
        }

        $token_member_id = $decode->data->member_id;

        if ($member_id != $token_member_id) {
            exception('账号信息错误', 401);
        } else {
            $member = MemberCache::get($member_id);

            if (empty($member)) {
                exception('账号登录状态失效', 401);
            } else {
                if ($token != $member['token']) {
                    exception('账号已在另一处登录', 401);
                } else {
                    if ($member['is_disable'] == 1) {
                        exception('账号已被禁用', 401);
                    }
                    if ($member['is_delete'] == 1) {
                        exception('账号已被删除', 401);
                    }
                }
            }
        }
    }
}
