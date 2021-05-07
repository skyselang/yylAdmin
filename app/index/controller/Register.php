<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2021-05-06
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\MemberValidate;
use app\common\service\SettingService;
use app\common\utils\VerifyUtils;
use app\index\service\RegisterService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("注册")
 * @Apidoc\Sort("2")
 */
class Register
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned(ref="returnVerify")
     */
    public function verify()
    {
        $setting = SettingService::verifyInfo();

        if ($setting['verify_register']) {
            $data = VerifyUtils::create();
        } else {
            $data['verify_switch'] = $setting['verify_register'];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\MemberModel\password")
     * @Apidoc\Param(ref="app\common\model\MemberModel\nickname")
     * @Apidoc\Param(ref="paramVerify")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function register()
    {
        $param['username']    = Request::param('username/s', '');
        $param['password']    = Request::param('password/s', '');
        $param['nickname']    = Request::param('nickname/s', '');
        $param['verify_id']   = Request::param('verify_id/s', '');
        $param['verify_code'] = Request::param('verify_code/s', '');

        $setting = SettingService::verifyInfo();
        if ($setting['verify_register']) {
            $check = VerifyUtils::check($param['verify_id'], $param['verify_code']);
            if (empty($check)) {
                exception('验证码错误');
            }
        }

        unset($param['verify_id'], $param['verify_code']);

        validate(MemberValidate::class)->scene('register')->check($param);

        $data = RegisterService::register($param);

        return success($data, '注册成功');
    }
}
