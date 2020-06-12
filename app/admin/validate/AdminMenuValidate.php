<?php
/*
 * @Description  : 菜单验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-25
 */

namespace app\admin\validate;

use think\Validate;

class AdminMenuValidate extends Validate
{
    protected $rule = [
        'admin_menu_id' => ['require'],
        'menu_name'     => ['require'],
    ];

    protected $message  =   [
        'admin_menu_id.require' => '缺少参数admin_menu_id',
        'menu_name.require'     => '请输入菜单名称',
    ];

    protected $scene = [
        'admin_menu_id' => ['admin_menu_id'],
        'menu_name'     => ['menu_name'],
        'menu_add'      => ['menu_name'],
        'menu_edit'     => ['admin_menu_id', 'menu_name'],
    ];
}
