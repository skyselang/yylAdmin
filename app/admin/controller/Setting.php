<?php
/*
 * @Description  : 基础设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\SettingValidate;
use app\common\service\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("基础设置")
 * @Apidoc\Group("index")
 */
class Setting
{
    /**
     * @Apidoc\Title("验证码信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned("verify_register", type="int", default="0", desc="注册验证码1开启0关闭"),
     *    @Apidoc\Returned("verify_login", type="int", default="0", desc="登录验证码1开启0关闭"),
     * )
     */
    public function verifyInfo()
    {
        $data = SettingService::verifyInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("verify_register", type="int", default="0", desc="注册验证码1开启0关闭")
     * @Apidoc\Param("verify_login", type="int", default="0", desc="登录验证码1开启0关闭")
     * @Apidoc\Returned(ref="return")
     */
    public function verifyEdit()
    {
        $param['verify_register'] = Request::param('verify_register/d', 0);
        $param['verify_login']    = Request::param('verify_login/d', 0);

        validate(SettingValidate::class)->scene('verify_edit')->check($param);

        $data = SettingService::verifyEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("Token信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned("token_exp", type="int", default="12", desc="token有效时间（小时）")
     * )
     */
    public function tokenInfo()
    {
        $data = SettingService::tokenInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("Token修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("token_exp", type="int", default="720", desc="token有效时间（小时）")
     * @Apidoc\Returned(ref="return")
     */
    public function tokenEdit()
    {
        $param['token_exp'] = Request::param('token_exp/d', 720);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::tokenEdit($param);

        return success($data);
    }
}
