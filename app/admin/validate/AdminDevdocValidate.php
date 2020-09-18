<?php
/*
 * @Description  : 开发文档验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-18
 * @LastEditTime : 2020-09-18
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class AdminDevdocValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_devdoc_id' => ['require'],
        'devdoc_name'     => ['require', 'checkDevdoc'],
    ];

    // 错误信息
    protected $message = [
        'admin_devdoc_id.require' => 'admin_devdoc_id must',
        'devdoc_name.require'     => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'admin_devdoc_id' => ['admin_devdoc_id'],
        'devdoc_name'     => ['devdoc_name'],
        'devdoc_add'      => ['devdoc_name'],
        'devdoc_edit'     => ['admin_devdoc_id', 'devdoc_name'],
    ];

    // 自定义验证规则：名称是否已存在
    protected function checkDevdoc($value, $rule, $data = [])
    {
        $admin_devdoc_id = isset($data['admin_devdoc_id']) ? $data['admin_devdoc_id'] : '';

        if ($admin_devdoc_id) {
            if ($data['devdoc_pid'] == $data['admin_devdoc_id']) {
                return '父级不能等于本身';
            }
        }

        $devdoc_name = Db::name('admin_devdoc')
            ->field('admin_devdoc_id')
            ->where('admin_devdoc_id', '<>', $admin_devdoc_id)
            ->where('devdoc_pid', '=', $data['devdoc_pid'])
            ->where('devdoc_name', '=', $data['devdoc_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($devdoc_name) {
            return '名称已存在：' . $data['devdoc_name'];
        }

        return true;
    }
}
