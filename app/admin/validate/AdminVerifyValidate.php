<?php
/*
 * @Description  : 验证码验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-15
 * @LastEditTime : 2020-09-05
 */

namespace app\admin\validate;

use think\Validate;
use app\admin\service\AdminVerifyService;

class AdminVerifyValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'verify_switch' => ['require', 'boolean'],
        'verify_curve'  => ['require', 'boolean'],
        'verify_noise'  => ['require', 'boolean'],
        'verify_bgimg'  => ['require', 'boolean'],
        'verify_type'   => ['require', 'between:1,5'],
        'verify_length' => ['require', 'between:3,6'],
        'verify_expire' => ['require', 'between:1,3600'],
        'verify_code'   => ['checkVerify'],
    ];

    // 错误信息
    protected $message  =   [
        'verify_switch.require' => '验证码开关状态错误',
        'verify_switch.boolean' => '验证码开关值错误',
        'verify_curve.require'  => '验证码曲线状态错误',
        'verify_curve.boolean'  => '验证码曲线值错误',
        'verify_noise.require'  => '验证码杂点状态错误',
        'verify_noise.boolean'  => '验证码杂点值错误',
        'verify_switch.require' => '验证码背景图状态错误',
        'verify_switch.boolean' => '验证码背景图值错误',
        'verify_type.require'   => '请选择验证码类型',
        'verify_type.between'   => '验证码类型错误',
        'verify_length.require' => '请选择验证码位数',
        'verify_length.between' => '验证码位数错误',
        'verify_expire.require' => '请输入验证码有效时间',
        'verify_expire.between' => '验证码有效时间范围：1-3600 秒',
    ];

    // 验证场景
    protected $scene = [
        'edit'  => ['verify_switch', 'verify_curve', 'verify_noise', 'verify_type', 'verify_bgimg', 'verify_length', 'verify_expire'],
        'check' => ['verify_code'],
    ];

    // 验证码验证
    protected function checkVerify($value, $rule, $data = [])
    {
        $admin_verify  = AdminVerifyService::config();
        $verify_id     = $data['verify_id'];
        $verify_code   = $data['verify_code'];
        $verify_switch = $admin_verify['verify_switch'];
        $verify_check  = false;

        if ($verify_switch) {
            if ($verify_code) {
                $AdminVerifyService = new AdminVerifyService();
                $check_verify = $AdminVerifyService->check($verify_id, $verify_code);
                
                if (empty($check_verify)) {
                    $verify_check = true;
                    $verify_msg   = '验证码错误';
                }
            } else {
                $verify_check = true;
                $verify_msg   = '请输入验证码';
            }
        }

        return $verify_check ? $verify_msg : true;
    }
}
