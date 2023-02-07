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
use app\common\model\file\FileModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 系统设置模型
 */
class SettingModel extends Model
{
    // 表名
    protected $name = 'system_setting';
    // 表主键
    protected $pk = 'setting_id';

    // 关联favicon文件
    public function favicon()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'favicon_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取favicon链接
    public function getFaviconUrlAttr()
    {
        return $this['favicon']['file_url'] ?? '';
    }

    // 关联logo文件
    public function logo()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'logo_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取logo链接
    public function getLogoUrlAttr()
    {
        return $this['logo']['file_url'] ?? '';
    }

    // 关联登录背景文件
    public function loginBg()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'login_bg_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取登录背景链接
    public function getLoginBgUrlAttr()
    {
        return $this['loginBg']['file_url'] ?? '';
    }
}
