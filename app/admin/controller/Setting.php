<?php
/*
 * @Description  : 设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-03-10
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\VerifyValidate;
use app\admin\validate\TokenValidate;
use app\admin\service\SettingService;

class Setting
{
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
            $data = SettingService::verify();
        } else {
            $param['type']   = Request::param('type/d', 1);
            $param['length'] = Request::param('length/d', 4);
            $param['expire'] = Request::param('expire/d', 180);
            $param['switch'] = Request::param('switch/b', false);
            $param['curve']  = Request::param('curve/b', false);
            $param['noise']  = Request::param('noise/b', false);
            $param['bgimg']  = Request::param('bgimg/b', false);

            validate(VerifyValidate::class)->scene('edit')->check($param);

            $data = SettingService::verify($param, 'post');
        }

        return success($data);
    }

    /**
     * Token设置
     *
     * @method GET|POST
     *
     * @return josn
     */
    public function settingToken()
    {
        if (Request::isGet()) {
            $data = SettingService::token();
        } else {
            $param['iss'] = Request::param('iss/s', 'yylAdmin');
            $param['exp'] = Request::param('exp/d', 7200);

            validate(TokenValidate::class)->scene('edit')->check($param);

            $data = SettingService::token($param, 'post');
        }

        return success($data);
    }
}
