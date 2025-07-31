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

/**
 * 系统设置模型
 */
class SettingModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_setting';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'setting_id';

    /**
     * 关联favicon文件
     * @return \think\model\relation\HasOne
     */
    public function favicon()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'favicon_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取favicon链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("favicon_url", type="string", desc="favicon链接")
     * @return string
     */
    public function getFaviconUrlAttr()
    {
        return $this['favicon']['file_url'] ?? '';
    }

    /**
     * 关联logo文件
     * @return \think\model\relation\HasOne
     */
    public function logo()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'logo_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取logo链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("logo_url", type="string", desc="logo链接")
     * @return string
     */
    public function getLogoUrlAttr()
    {
        return $this['logo']['file_url'] ?? '';
    }

    /**
     * 关联登录背景文件
     * @return \think\model\relation\HasOne
     */
    public function loginbg()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'login_bg_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取登录背景链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("login_bg_url", type="string", desc="登录背景链接")
     * @return string
     */
    public function getLoginBgUrlAttr()
    {
        return $this['loginbg']['file_url'] ?? '';
    }

    /**
     * 修改日志请求参数排除字段
     * @param mixed $value 数据
     * @return string
     */
    public function setLogParamWithoutAttr($value)
    {
        $value = trim(str_replace('，', ',', $value), ',');
        return $value;
    }
}
