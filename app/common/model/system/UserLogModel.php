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
 * 用户日志模型
 */
class UserLogModel extends Model
{
    // 表名
    protected $name = 'system_user_log';
    // 表主键
    protected $pk = 'log_id';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }

    /**
     * 获取日志类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("log_type_name", type="string", desc="日志类型名称")
     */
    public function getLogTypeNameAttr($value, $data)
    {
        return SettingService::logTypes($data['log_type']);
    }

    // 关联用户
    public function user()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'user_id');
    }
    /**
     * 获取用户昵称
     * @Apidoc\Field("")
     * @Apidoc\AddField("nickname", type="string", desc="用户昵称")
     */
    public function getNicknameAttr()
    {
        return $this['user']['nickname'] ?? '';
    }
    /**
     * 获取用户账号
     * @Apidoc\Field("")
     * @Apidoc\AddField("username", type="string", desc="用户账号")
     */
    public function getUsernameAttr()
    {
        return $this['user']['username'] ?? '';
    }

    // 关联菜单
    public function menu()
    {
        return $this->hasOne(MenuModel::class, 'menu_id', 'menu_id');
    }
    /**
     * 获取菜单名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("menu_name", type="string", desc="菜单名称")
     */
    public function getMenuNameAttr($value, $data)
    {
        $menu_name = $this['menu']['menu_name'] ?? '';
        if ($menu_name) {
            return $menu_name;
        }
        return $data['menu_name'] ?? '';
    }
    /**
     * 获取菜单链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("menu_url", type="string", desc="菜单链接")
     */
    public function getMenuUrlAttr($value, $data)
    {
        return $this['menu']['menu_url'] ?? $data['request_url'] ?? '';
    }

    /**
     * 修改请求方法
     */
    public function setRequestMethodAttr($value)
    {
        return strtoupper($value);
    }

    // 修改请求参数
    public function setRequestParamAttr($value)
    {
        return json_encode($value);
    }
    // 获取请求参数
    public function getRequestParamAttr($value)
    {
        return json_decode($value, true);
    }
}
