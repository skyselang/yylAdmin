<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\service;

use app\common\service\member\MemberService;
use app\common\service\setting\SettingService;
use app\common\service\member\LogService;

/**
 * 注册
 */
class RegisterService
{
    /**
     * 账号注册
     *
     * @param array $param 注册信息
     *
     * @return array
     */
    public static function register($param)
    {
        $setting = SettingService::info();
        if (!$setting['is_register']) {
            exception('系统维护，无法注册！');
        }

        if (empty($param['username'])) {
            $param['username'] = md5(uniqid(mt_rand(), true));
        }
        $data = MemberService::add($param);

        $member_log['member_id'] = $data['member_id'];
        LogService::add($member_log, 1);

        return $data;
    }
}
