<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\service;

use hg\apidoc\annotation as Apidoc;
use app\common\service\member\MemberService;
use app\common\service\member\SettingService;

/**
 * 注册
 */
class RegisterService
{
    /**
     * 账号注册
     * @param array $param 注册信息
     * @param bool  $login 是否登录
     * @return array
     * @Apidoc\Param(ref={MemberService::class,"edit"})
     */
    public static function register($param, $login = null)
    {
        if (!isset($param['platform'])) {
            $param['platform'] = member_platform();
        }
        if (!isset($param['application'])) {
            $param['application'] = member_application();
        }
        if (empty($param['username'])) {
            $param['username'] = uniqids();
        }

        unset($param['captcha_id'], $param['captcha_code'], $param['ajcaptcha']);
        $data = MemberService::add($param);
        unset($data['password'], $data['create_uid']);

        if ($login === null) {
            $setting = SettingService::info();
            $login   = $setting['is_auto_login'] ? true : false;
        }
        if ($login) {
            $data = MemberService::login($data, 'register');
        }

        return $data;
    }
}
