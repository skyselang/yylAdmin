<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-09
 */

namespace app\admin\service;

use think\facade\Config;
use app\cache\AdminUserCache;
use Firebase\JWT\JWT;

class AdminTokenService
{
    /**
     * Token生成
     * 
     * @param array $admin_user 用户数据
     * 
     * @return string
     */
    public static function create($admin_user = [])
    {
        $admin_setting = AdminSettingService::admin_setting();
        $admin_token   = $admin_setting['admin_token'];

        $key = Config::get('admin.token_key');; //密钥
        $iss = $admin_token['iss']; //签发者
        $iat = time(); //签发时间
        $nbf = time(); //生效时间
        $exp = time() + $admin_token['exp'] * 60 * 60; //过期时间

        $data = [
            'admin_user_id' => $admin_user['admin_user_id'],
            'login_time'    => $admin_user['login_time'],
            'login_ip'      => $admin_user['login_ip'],
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
     * @param string  $token         token
     * @param integer $admin_user_id 用户id
     * 
     * @return json
     */
    public static function verify($token, $admin_user_id = 0)
    {
        try {
            $key     = Config::get('admin.token_key');
            $decoded = JWT::decode($token, $key, array('HS256'));

            if (!super_admin($admin_user_id)) {
                $token_admin_user_id = $decoded->data->admin_user_id;

                if ($admin_user_id != $token_admin_user_id) {
                    error('账号信息错误', 'Token：登录账号和请求账号不一致', 401);
                } else {
                    $admin_user = AdminUserCache::get($admin_user_id);
                    if (empty($admin_user)) {
                        error('账号登录状态失效', 'Token：账号登录状态已经过期', 401);
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
