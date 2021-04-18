<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-04-17
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\service\VerifyService;
use app\common\service\SettingService;
use app\common\validate\MemberValidate;
use app\common\validate\VerifyValidate;
use app\index\service\LoginService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 */
class Login
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned(ref="returnVerify")
     */
    public function verify()
    {
        $config = SettingService::info();

        $verify = $config['verify'];

        $data = VerifyService::create($verify);

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\MemberModel\password")
     * @Apidoc\Param(ref="paramVerify")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\MemberModel\login")
     * )
     */
    public function login()
    {
        $param['username']    = Request::param('username/s', '');
        $param['password']    = Request::param('password/s', '');
        $param['verify_id']   = Request::param('verify_id/s', '');
        $param['verify_code'] = Request::param('verify_code/s', '');

        $config = SettingService::info();
        $verify = $config['verify'];
        if ($verify['switch']) {
            validate(VerifyValidate::class)->scene('check')->check($param);
        }

        validate(MemberValidate::class)->scene('login')->check($param);

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\Returned(ref="return")
     */
    public function logout()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('logout')->check($param);

        $data = LoginService::logout($param['member_id']);

        return success($data, '退出成功');
    }
}
