<?php
/*
 * @Description  : 系统设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-04
 * @LastEditTime : 2020-09-04
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminVerifyValidate;
use app\admin\service\AdminSettingService;

class AdminSetting
{
    /**
     * 缓存设置
     *
     * @method GET
     *
     * @return json
     */
    public function settingCache()
    {
        $data = AdminSettingService::settingCache();

        return success($data);
    }

    /**
     * 验证码设置
     *
     * @method GET|POST
     *
     * @return josn
     */
    public function settingVerify()
    {
        if (Request::isGet()) {
            $data = AdminSettingService::settingVerify();
        } else {
            $param = Request::only(
                [
                    'verify_type'   => 1,
                    'verify_length' => 4,
                    'verify_expire' => 180,
                ]
            );
            $param['verify_switch'] = Request::param('verify_switch/b', false);
            $param['verify_curve']  = Request::param('verify_curve/b', false);
            $param['verify_noise']  = Request::param('verify_noise/b', false);
            $param['verify_bgimg']  = Request::param('verify_bgimg/b', false);

            validate(AdminVerifyValidate::class)->scene('edit')->check($param);

            $data = AdminSettingService::settingVerify($param);
        }

        return success($data);
    }
}
