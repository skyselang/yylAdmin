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
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_user_log';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'log_id';

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
     * 获取日志类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("log_type_name", type="string", desc="日志类型名称")
     * @return string
     */
    public function getLogTypeNameAttr($value, $data)
    {
        return SettingService::logTypes($data['log_type']);
    }

    /**
     * 关联用户
     * @Apidoc\Field("")
     * @Apidoc\AddField("user_nickname", type="string", desc="用户昵称")
     * @Apidoc\AddField("user_username", type="string", desc="用户账号")
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'user_id');
    }
    /**
     * 获取用户昵称
     * @Apidoc\Field("")
     * @Apidoc\AddField("user_nickname", type="string", desc="用户昵称")
     * @return string
     */
    public function getUserNicknameAttr()
    {
        return $this['user']['nickname'] ?? '';
    }
    /**
     * 获取用户账号
     * @Apidoc\Field("")
     * @Apidoc\AddField("user_username", type="string", desc="用户账号")
     * @return string
     */
    public function getUserUsernameAttr()
    {
        return $this['user']['username'] ?? '';
    }

    /**
     * 关联菜单
     * @Apidoc\Field("")
     * @Apidoc\AddField("menu_name", type="string", desc="菜单名称")
     * @Apidoc\AddField("menu_url", type="string", desc="菜单链接")
     * @return \think\model\relation\HasOne
     */
    public function menu()
    {
        return $this->hasOne(MenuModel::class, 'menu_id', 'menu_id');
    }
    /**
     * 获取菜单名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("menu_name", type="string", desc="菜单名称")
     * @return string
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
     * @return string
     */
    public function getMenuUrlAttr($value, $data)
    {
        return $this['menu']['menu_url'] ?? $data['request_url'] ?? '';
    }

    /**
     * 修改请求方法
     * @param mixed $value 数据
     * @return string
     */
    public function setRequestMethodAttr($value)
    {
        return strtoupper($value);
    }

    /**
     * 修改请求参数
     * @param mixed $value 数据
     * @return string
     */
    public function setRequestParamAttr($value)
    {
        return json_encode($value);
    }
    /**
     * 获取请求参数
     * @param mixed $value 数据
     * @return mixed
     */
    public function getRequestParamAttr($value)
    {
        if (empty($value)) {
            $value = '{}';
        }
        return json_decode($value, true);
    }
}
