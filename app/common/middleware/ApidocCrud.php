<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// Apidoc CRUD 中间件
namespace app\common\middleware;

use app\common\service\admin\MenuService;

class ApidocCrud
{
    // 生成文件及数据表前执行
    public function before($tplParams)
    {
        // 如果在此方法内改变了 tplParams 参数，将其返回，就可以在模板中使用了
        return $tplParams;
    }

    // 生成文件及数据表后执行
    public function after($tplParams)
    {
        trace($tplParams, 'error');
        $add_menu = [
            'menu_pid'   => 0,
            'menu_type'  => 2,
            'meta_icon'  => 'el-icon-menu',
            'menu_name'  => $tplParams['form']['controller_title'],
            'menu_url'   => $tplParams['app'][0]['folder'] . '/' . $tplParams['controller']['class_name'] . '/list',
            'path'       => '/' . $tplParams['controller']['class_name'],
            'component'  => strtolower($tplParams['controller']['class_name']) . '/' . strtolower($tplParams['controller']['class_name']),
            'name'       => $tplParams['controller']['class_name'],
            'meta_query' => '',
            'hidden'     => 0,
            'menu_sort'  => 250,
            'add_info'   => true,
            'add_add'    => true,
            'add_edit'   => true,
            'add_dele'   => true,
        ];

        MenuService::add($add_menu);
    }
}
