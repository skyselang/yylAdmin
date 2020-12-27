<?php
/*
 * @Description  : Token
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\service;

use think\facade\Config;
use app\common\cache\AdminUserCache;
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

        $key = Config::get('admin.token_key');       //密钥
        $iss = $admin_token['iss'];                  //签发者
        $iat = time();                               //签发时间
        $nbf = time();                               //生效时间
        $exp = time() + $admin_token['exp'] * 3600;  //过期时间

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
            $key    = Config::get('admin.token_key');
            $decode = JWT::decode($token, $key, array('HS256'));
        } catch (\Exception $e) {
            exception('账号登录状态已过期', 401);
        }

        $admin_user_id_token = $decode->data->admin_user_id;

        if ($admin_user_id != $admin_user_id_token) {
            exception('账号请求信息错误', 401);
        } else {
            $admin_user = AdminUserCache::get($admin_user_id);

            if (empty($admin_user)) {
                exception('账号登录状态失效', 401);
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
    }
}
