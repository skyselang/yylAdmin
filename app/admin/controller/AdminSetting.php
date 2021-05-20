<?php
/*
 * @Description  : 设置管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-05
 * @LastEditTime : 2021-05-20
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
     * @Apidoc\Title("缓存设置信息")
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
     * @Apidoc\Title("缓存设置清除")
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
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\AdminSettingModel\verifyInfo"),
     * )
     */
    public function verifyInfo()
    {
        $data = AdminSettingService::verifyInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminSettingModel\verifyInfo")
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
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\AdminSettingModel\tokenInfo")
     * )
     */
    public function tokenInfo()
    {
        $data = AdminSettingService::tokenInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminSettingModel\tokenInfo")
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
