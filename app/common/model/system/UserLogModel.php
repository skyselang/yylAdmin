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
 * 用户日志模型
 */
class UserLogModel extends Model
{
    // 表名
    protected $name = 'system_user_log';
    // 表主键
    protected $pk = 'log_id';

    // 修改请求参数
    public function setRequestParamAttr($value)
    {
        return serialize($value);
    }
    // 获取请求参数
    public function getRequestParamAttr($value)
    {
        return unserialize($value);
    }

    // 关联用户
    public function user()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'user_id');
    }
    // 获取用户昵称
    public function getNicknameAttr()
    {
        return $this['user']['nickname'] ?? '';
    }
    // 获取用户账号
    public function getUsernameAttr()
    {
        return $this['user']['username'] ?? '';
    }

    // 关联菜单
    public function menu()
    {
        return $this->hasOne(MenuModel::class, 'menu_id', 'menu_id');
    }
    // 获取菜单名称
    public function getMenuNameAttr()
    {
        return $this['menu']['menu_name'] ?? '';
    }
    // 获取菜单链接
    public function getMenuUrlAttr()
    {
        return $this['menu']['menu_url'] ?? '';
    }
}
