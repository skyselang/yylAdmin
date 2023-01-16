<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use app\common\service\system\UserService;
use app\common\service\system\SettingService;

/**
 * 接口文档
 */
class ApidocService
{
    /**
     * 接口文档
     *
     * @return array
     */
    public static function apidoc()
    {
        $system    = SettingService::info();
        $user      = UserService::info(user_id());
        $token     = $user[$system['token_name']];
        $token_sub = substr($token, 0, 16) . '...';

        $data['apidoc_url'] = server_url() . '/apidoc';
        $data['apidoc_pwd'] = config('apidoc.auth.password');
        $data['user_id']    = $user['user_id'];
        $data['token']      = $token;
        $data['token_sub']  = $token_sub;

        return $data;
    }
}
