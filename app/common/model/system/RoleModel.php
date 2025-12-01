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
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_role';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'role_id';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     * @return string
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? lang('是') : lang('否');
    }

    /**
     * 关联菜单
     * @return \think\model\relation\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany(MenuModel::class, RoleMenusModel::class, 'menu_id', 'role_id');
    }
    /**
     * 获取菜单id
     * @Apidoc\Field("")
     * @Apidoc\AddField("menu_ids", type="array", desc="菜单id")
     * @return array
     */
    public function getMenuIdsAttr()
    {
        return model_relation_fields($this['menus'], 'menu_id');
    }
}
