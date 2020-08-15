<?php
/*
 * @Description  : token
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-08-14
 */

namespace app\admin\service;

use app\cache\AdminUserCache;
use think\facade\Config;
use Firebase\JWT\JWT;

class AdminTokenService
{
    /**
     * token生成
     * 
     * @param array $admin_user 用户信息
     * 
     * @return string
     */
    public static function create($admin_user = [])
    {
        $data = [
            'admin_user_id' => $admin_user['admin_user_id'],
            'login_time'    => $admin_user['login_time'],
            'login_ip'      => $admin_user['login_ip'],
        ];

        $key = Config::get('admin.token.key');
        $iss = Config::get('admin.token.iss');
        $iat = Config::get('admin.token.iat');
        $exp = Config::get('admin.token.exp');

        $payload = [
            'iss'  => $iss,
            'iat'  => $iat,
            'exp'  => $exp,
            'data' => $data,
        ];

        $token = JWT::encode($payload, $key);

        return $token;
    }

    /**
     * token验证
     *
     * @param string  $token         token
     * @param integer $admin_user_id 用户id
     * 
     * @return json
     */
    public static function verify($token, $admin_user_id = '')
    {
        try {
            $key     = Config::get('admin.token.key');
            $decoded = JWT::decode($token, $key, array('HS256'));

            if (!super_admin($admin_user_id)) {
                $token_admin_user_id = $decoded->data->admin_user_id;

                if ($admin_user_id != $token_admin_user_id) {
                    error('账号信息错误', 'Token：登录账号和请求账号不一致', 401);
                } else {
                    $admin_user = AdminUserCache::get($admin_user_id);
                    if (empty($admin_user)) {
                        error('账号登录状态失效', 'Token：账号登录状态已过期', 401);
                    } else {
                        if ($token != $admin_user['admin_token']) {
                            error('账号已在另一处登录', 'Token：账号已在另一处登录', 401);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            error('账号登录状态错误', 'Token：' . $e->getMessage(), 401);
        }
    }
}
