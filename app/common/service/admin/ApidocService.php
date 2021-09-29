<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口文档
namespace app\common\service\admin;

use app\common\service\admin\UserService;

class ApidocService
{
    /**
     * 接口文档
     *
     * @return array
     */
    public static function apidoc()
    {
        $admin_user      = UserService::info(admin_user_id());
        $admin_token     = $admin_user['admin_token'];
        $admin_token_sub = substr($admin_token, 0, 16) . '...';

        $data['apidoc_url']      = server_url() . '/apidoc';
        $data['apidoc_pwd']      = config('apidoc.auth.password');
        $data['admin_user_id']   = $admin_user['admin_user_id'];
        $data['admin_token']     = $admin_token;
        $data['admin_token_sub'] = $admin_token_sub;

        return $data;
    }
}
