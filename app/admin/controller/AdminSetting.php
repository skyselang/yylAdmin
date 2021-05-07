<?php
/*
 * @Description  : 设置管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-05
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminSettingValidate;
use app\common\service\AdminSettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("admin")
 */
class AdminSetting
{
    /**
     * @Apidoc\Title("缓存信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned("type", type="string", default="", desc="缓存类型"),
     * )
     */
    public function cacheInfo()
    {
        $data = AdminSettingService::cacheInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("缓存清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     */
    public function cacheClear()
    {
        $data = AdminSettingService::cacheClear();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned("verify_switch", type="int", default="0", desc="验证码是否开启1开启0关闭"),
     * )
     */
    public function verifyInfo()
    {
        $data = AdminSettingService::verifyInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("verify_switch", type="int", default="0", desc="验证码是否开启1开启0关闭")
     * @Apidoc\Returned(ref="return")
     */
    public function verifyEdit()
    {
        $param['verify_switch'] = Request::param('verify_switch/d', 0);

        validate(AdminSettingValidate::class)->scene('verify_edit')->check($param);

        $data = AdminSettingService::verifyEdit($param);

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
        $data = AdminSettingService::tokenInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("Token修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("token_exp", type="int", default="12", desc="token有效时间（小时）")
     * @Apidoc\Returned(ref="return")
     */
    public function tokenEdit()
    {
        $param['token_exp'] = Request::param('token_exp/d', 12);

        validate(AdminSettingValidate::class)->scene('token_edit')->check($param);

        $data = AdminSettingService::tokenEdit($param);

        return success($data);
    }
}
