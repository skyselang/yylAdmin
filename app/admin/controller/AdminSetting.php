<?php
/*
 * @Description  : 系统设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-05
 * @LastEditTime : 2021-04-17
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminVerifyValidate;
use app\common\validate\AdminTokenValidate;
use app\common\service\AdminSettingService;
use app\common\service\VerifyService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("系统设置")
 * @Apidoc\Group("admin")
 */
class AdminSetting
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("verify", type="object", desc="验证码配置",
     *          @Apidoc\Returned("switch", type="bool", default="false", desc="验证码是否开启"),
     *          @Apidoc\Returned("type", type="int", default="1", desc="验证码类型"),
     *          @Apidoc\Returned("length", type="int", default="4", desc="验证码长度"),
     *          @Apidoc\Returned("expire", type="int", default="180", desc="验证码有效时间（秒）"),
     *          @Apidoc\Returned("curve", type="bool", default="false", desc="验证码是否开启曲线"),
     *          @Apidoc\Returned("noise", type="bool", default="false", desc="验证码是否开启杂点"),
     *          @Apidoc\Returned("bgimg", type="bool", default="false", desc="验证码是否开启背景图"),
     *      ),
     *      @Apidoc\Returned("token", type="object", desc="token配置",
     *          @Apidoc\Returned("iss", type="bool", default="false", desc="token签发者"),
     *          @Apidoc\Returned("exp", type="int", default="12", desc="token有效时间（小时）"),
     *      ),
     * )
     */
    public function info()
    {
        $data = AdminSettingService::info();

        $verify = VerifyService::create($data['verify']);

        $data['verify'] = array_merge($data['verify'], $verify);

        return success($data);
    }

    /**
     * @Apidoc\Title("缓存设置")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     */
    public function cache()
    {
        $data = AdminSettingService::cache();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("switch", type="bool", default="false", desc="验证码是否开启"),
     * @Apidoc\Param("type", type="int", default="1", desc="验证码类型"),
     * @Apidoc\Param("length", type="int", default="4", desc="验证码长度"),
     * @Apidoc\Param("expire", type="int", default="180", desc="验证码有效时间（秒）"),
     * @Apidoc\Param("curve", type="bool", default="false", desc="验证码是否开启曲线"),
     * @Apidoc\Param("noise", type="bool", default="false", desc="验证码是否开启杂点"),
     * @Apidoc\Param("bgimg", type="bool", default="false", desc="验证码是否开启背景图"),
     * @Apidoc\Returned(ref="return")
     */
    public function verify()
    {
        $param['switch'] = Request::param('switch/b', false);
        $param['type']   = Request::param('type/d', 1);
        $param['length'] = Request::param('length/d', 4);
        $param['expire'] = Request::param('expire/d', 180);
        $param['curve']  = Request::param('curve/b', false);
        $param['noise']  = Request::param('noise/b', false);
        $param['bgimg']  = Request::param('bgimg/b', false);

        validate(AdminVerifyValidate::class)->scene('edit')->check($param);

        $data = AdminSettingService::verify($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("iss", type="string", default="", desc="token签发者")
     * @Apidoc\Param("exp", type="int", default="12", desc="token有效时间（小时）")
     * @Apidoc\Returned(ref="return")
     */
    public function token()
    {
        $param['iss'] = Request::param('iss/s', 'yylAdmin');
        $param['exp'] = Request::param('exp/d', 12);

        validate(AdminTokenValidate::class)->scene('edit')->check($param);

        $data = AdminSettingService::token($param);

        return success($data);
    }
}
