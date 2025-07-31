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
use hg\apidoc\annotation as Apidoc;
use app\common\model\file\FileModel;

/**
 * 设置管理模型
 */
class SettingModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'setting_setting';
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
     * 关联公众号二维码文件
     * @return \think\model\relation\HasOne
     */
    public function offi()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'offi_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取公众号二维码链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("offi_url", type="string", desc="公众号二维码链接")
     * @return string
     */
    public function getOffiUrlAttr()
    {
        return $this['offi']['file_url'] ?? '';
    }

    /**
     * 关联小程序码文件
     * @return \think\model\relation\HasOne
     */
    public function mini()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'mini_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取小程序码链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("mini_url", type="string", desc="小程序码链接")
     * @return string
     */
    public function getMiniUrlAttr()
    {
        return $this['mini']['file_url'] ?? '';
    }

    /**
     * 关联视频号文件
     * @return \think\model\relation\HasOne
     */
    public function video()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'video_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取视频号链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("video_url", type="string", desc="视频号链接")
     * @return string
     */
    public function getVideoUrlAttr()
    {
        return $this['video']['file_url'] ?? '';
    }

    /**
     * 关联抖音号文件
     * @return \think\model\relation\HasOne
     */
    public function douyin()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'douyin_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取抖音号链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("douyin_url", type="string", desc="抖音号链接")
     * @return string
     */
    public function getDouyinUrlAttr()
    {
        return $this['douyin']['file_url'] ?? '';
    }
}
