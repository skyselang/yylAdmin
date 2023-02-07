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
use hg\apidoc\annotation as Apidoc;

/**
 * 角色管理模型
 */
class RoleModel extends Model
{
    // 表名
    protected $name = 'system_role';
    // 表主键
    protected $pk = 'role_id';

    // 关联菜单
    public function menus()
    {
        return $this->belongsToMany(MenuModel::class, RoleMenusModel::class, 'menu_id', 'role_id');
    }
    // 获取菜单id
    public function getMenuIdsAttr()
    {
        return relation_fields($this['menus'], 'menu_id');
    }
}
