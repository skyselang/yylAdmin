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

    // 关联公众号文件
    public function offi()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'offi_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取公众号链接
    public function getOffiUrlAttr()
    {
        return $this['offi']['file_url'] ?? '';
    }

    // 关联小程序文件
    public function mini()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'mini_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取小程序链接
    public function getMiniUrlAttr()
    {
        return $this['mini']['file_url'] ?? '';
    }

    // 修改自定义设置
    public function setDiyConfigAttr($value)
    {
        return serialize($value);
    }
    // 获取自定义设置
    public function getDiyConfigAttr($value)
    {
        return unserialize($value);
    }
    // 获取自定义设置对象
    public function getDiyConObjAttr($value, $data)
    {
        $diy_config = is_array($data['diy_config']) ? $data['diy_config'] : unserialize($data['diy_config']);
        foreach ($diy_config as $diy) {
            $diy_con_obj[$diy['config_key']] = $diy['config_val'];
        }
        return $diy_con_obj ?? [];
    }

    // 获取反馈类型
    public function getFeedbackTypeAttr()
    {
        return SettingService::feedback_types();
    }
}
