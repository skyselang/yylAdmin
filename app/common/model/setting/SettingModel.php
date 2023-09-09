<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\setting;

use think\Model;
use app\common\model\file\FileModel;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 设置管理模型
 */
class SettingModel extends Model
{
    // 表名
    protected $name = 'setting_setting';
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

    // 关联公众号二维码文件
    public function offi()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'offi_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取公众号二维码链接
    public function getOffiUrlAttr()
    {
        return $this['offi']['file_url'] ?? '';
    }

    // 关联小程序码文件
    public function mini()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'mini_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取小程序码链接
    public function getMiniUrlAttr()
    {
        return $this['mini']['file_url'] ?? '';
    }

    // 获取反馈类型
    public function getFeedbackTypeAttr()
    {
        return SettingService::feedbackTypes();
    }
}
