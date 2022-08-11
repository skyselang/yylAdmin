<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\BaseController;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("100")
 */
class Setting extends BaseController
{

    /**
     * @Apidoc\Title("验证码设置")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel\captchaInfoParam")
     */
    public function captcha()
    {
        $setting = SettingService::info();

        $data['captcha_register'] = $setting['captcha_register'];
        $data['captcha_login']    = $setting['captcha_login'];

        return success($data);
    }

    /**
     * @Apidoc\Title("自定义设置")
     */
    public function diy()
    {
        $data = SettingService::diy();

        return success($data);
    }
}
