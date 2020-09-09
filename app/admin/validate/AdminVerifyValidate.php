<?php
/*
 * @Description  : 验证码验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-15
 * @LastEditTime : 2020-09-09
 */

namespace app\admin\validate;

use think\Validate;
use app\admin\service\AdminVerifyService;

class AdminVerifyValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'switch'      => ['require', 'boolean'],
        'curve'       => ['require', 'boolean'],
        'noise'       => ['require', 'boolean'],
        'bgimg'       => ['require', 'boolean'],
        'type'        => ['require', 'between:1,5'],
        'length'      => ['require', 'between:3,6'],
        'expire'      => ['require', 'between:1,3600'],
        'verify_code' => ['checkVerify'],
    ];

    // 错误信息
    protected $message  =   [
        'switch.require' => '验证码开关状态错误',
        'switch.boolean' => '验证码开关值错误',
        'curve.require'  => '验证码曲线状态错误',
        'curve.boolean'  => '验证码曲线值错误',
        'noise.require'  => '验证码杂点状态错误',
        'noise.boolean'  => '验证码杂点值错误',
        'bgimg.require'  => '验证码背景图状态错误',
        'bgimg.boolean'  => '验证码背景图值错误',
        'type.require'   => '请选择验证码类型',
        'type.between'   => '验证码类型错误',
        'length.require' => '请选择验证码位数',
        'length.between' => '验证码位数错误',
        'expire.require' => '请输入验证码有效时间',
        'expire.between' => '验证码有效时间范围：1-3600 秒',
    ];

    // 验证场景
    protected $scene = [
        'edit'  => ['switch', 'curve', 'noise', 'type', 'bgimg', 'length', 'expire'],
        'check' => ['verify_code'],
    ];

    // 验证码验证
    protected function checkVerify($value, $rule, $data = [])
    {
        $admin_verify = AdminVerifyService::config();
        $verify_id    = $data['verify_id'];
        $verify_code  = $data['verify_code'];
        $switch       = $admin_verify['switch'];

        if ($switch) {
            if (empty($verify_code)) {
                return '请输入验证码';
            } else {
                $AdminVerifyService = new AdminVerifyService();
                $check_verify = $AdminVerifyService->check($verify_id, $verify_code);
                if (empty($check_verify)) {
                    return '验证码错误';
                }
            }
        }

        return true;
    }
}
