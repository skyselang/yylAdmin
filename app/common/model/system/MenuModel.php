<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\system;

use think\Model;
use app\common\service\system\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 菜单管理模型
 */
class MenuModel extends Model
{
    // 表名
    protected $name = 'system_menu';
    // 表主键
    protected $pk = 'menu_id';

    public function getMenuTypeNameAttr($value, $data)
    {
        return SettingService::menuTypes($data['menu_type'] ?? 0);
    }
}
