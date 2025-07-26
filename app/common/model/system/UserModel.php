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
use app\common\model\file\FileModel;
use app\common\service\system\SettingService;

/**
 * 用户管理模型
 */
class UserModel extends Model
{
    // 表名
    protected $name = 'system_user';
    // 表主键
    protected $pk = 'user_id';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }

    // 关联头像文件
    public function avatar()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'avatar_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取头像链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("avatar_url", type="string", desc="头像链接")
     */
    public function getAvatarUrlAttr()
    {
        return $this['avatar']['file_url'] ?? '';
    }

    /**
     * 获取性别名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("gender_name", type="string", desc="性别名称")
     */
    public function getGenderNameAttr($value, $data)
    {
        return SettingService::genders($data['gender']);
    }

    /**
     * 获取年龄
     * @Apidoc\Field("")
     * @Apidoc\AddField("age", type="int", desc="年龄")
     */
    public function getAgeAttr($value, $data)
    {
        if ($data['birthday'] ?? '') {
            return date('Y') - date('Y', strtotime($data['birthday']));
        }
        return '';
    }

    // 关联部门
    public function depts()
    {
        return $this->belongsToMany(DeptModel::class, UserAttributesModel::class, 'dept_id', 'user_id');
    }
    /**
     * 获取部门id
     * @Apidoc\Field("")
     * @Apidoc\AddField("dept_ids", type="array", desc="部门id")
     */
    public function getDeptIdsAttr()
    {
        return model_relation_fields($this['depts'], 'dept_id');
    }
    /**
     * 获取部门名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("dept_names", type="string", desc="部门名称")
     */
    public function getDeptNamesAttr()
    {
        return model_relation_fields($this['depts'], 'dept_name', true);
    }

    // 关联职位
    public function posts()
    {
        return $this->belongsToMany(PostModel::class, UserAttributesModel::class, 'post_id', 'user_id');
    }
    /**
     * 获取职位id
     * @Apidoc\Field("")
     * @Apidoc\AddField("post_ids", type="array", desc="职位id")
     */
    public function getPostIdsAttr()
    {
        return model_relation_fields($this['posts'], 'post_id');
    }
    /**
     * 获取职位名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("post_names", type="string", desc="职位名称")
     */
    public function getPostNamesAttr()
    {
        return model_relation_fields($this['posts'], 'post_name', true);
    }

    // 关联角色
    public function roles()
    {
        return $this->belongsToMany(RoleModel::class, UserAttributesModel::class, 'role_id', 'user_id');
    }
    /**
     * 获取角色id
     * @Apidoc\Field("")
     * @Apidoc\AddField("role_ids", type="array", desc="角色id")
     */
    public function getRoleIdsAttr()
    {
        return model_relation_fields($this['roles'], 'role_id');
    }
    /**
     * 获取角色名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("role_names", type="string", desc="角色名称")
     */
    public function getRoleNamesAttr()
    {
        return model_relation_fields($this['roles'], 'role_name', true);
    }

    /**
     * 获取是否超管名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_super_name", type="string", desc="是否超管名称")
     */
    public function getIsSuperNameAttr($value, $data)
    {
        return ($data['is_super'] ?? 0) ? '是' : '否';
    }
}
