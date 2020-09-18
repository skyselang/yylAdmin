<?php
/*
 * @Description  : 接口文档验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-17
 * @LastEditTime : 2020-09-18
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class AdminApidocValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_apidoc_id' => ['require'],
        'apidoc_name'     => ['require', 'checkApidoc'],
    ];

    // 错误信息
    protected $message = [
        'admin_apidoc_id.require' => 'admin_apidoc_id must',
        'apidoc_name.require'     => '请输入接口名称',
    ];

    // 验证场景
    protected $scene = [
        'admin_apidoc_id' => ['admin_apidoc_id'],
        'apidoc_name'     => ['apidoc_name'],
        'apidoc_add'      => ['apidoc_name'],
        'apidoc_edit'     => ['admin_apidoc_id', 'apidoc_name'],
    ];

    // 自定义验证规则：接口是否已存在
    protected function checkApidoc($value, $rule, $data = [])
    {
        $admin_apidoc_id = isset($data['admin_apidoc_id']) ? $data['admin_apidoc_id'] : '';

        if ($admin_apidoc_id) {
            if ($data['apidoc_pid'] == $data['admin_apidoc_id']) {
                return '接口父级不能等于接口本身';
            }
        }

        $apidoc_name = Db::name('admin_apidoc')
            ->field('admin_apidoc_id')
            ->where('admin_apidoc_id', '<>', $admin_apidoc_id)
            ->where('apidoc_pid', '=', $data['apidoc_pid'])
            ->where('apidoc_name', '=', $data['apidoc_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($apidoc_name) {
            return '接口名称已存在：' . $data['apidoc_name'];
        }

        return true;
    }
}
