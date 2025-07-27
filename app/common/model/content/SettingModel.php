<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\content;

use think\Model;
use hg\apidoc\annotation as Apidoc;
use app\common\model\file\FileModel;

/**
 * 内容设置模型
 */
class SettingModel extends Model
{
    // 表名
    protected $name = 'content_setting';
    // 表主键
    protected $pk = 'setting_id';

    // 关联favicon文件
    public function favicon()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'favicon_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取favicon链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("favicon_url", type="string", desc="favicon链接")
     */
    public function getFaviconUrlAttr($value, $data)
    {
        return $this['favicon']['file_url'] ?? '';
    }

    // 关联logo文件
    public function logo()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'logo_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取logo链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("logo_url", type="string", desc="logo链接")
     */
    public function getLogoUrlAttr($value, $data)
    {
        return $this['logo']['file_url'] ?? '';
    }

    // 关联公众号二维码文件
    public function offi()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'offi_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取公众号二维码链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("offi_url", type="string", desc="公众号二维码链接")
     */
    public function getOffiUrlAttr($value, $data)
    {
        return $this['offi']['file_url'] ?? '';
    }

    // 关联小程序码文件
    public function mini()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'mini_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取小程序码链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("mini_url", type="string", desc="小程序码链接")
     */
    public function getMiniUrlAttr($value, $data)
    {
        return $this['mini']['file_url'] ?? '';
    }

    // 关联内容默认图片文件
    public function contentDefaultImg()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'content_default_img_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取内容默认图片链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("content_default_img_url", type="string", desc="内容默认图片链接")
     */
    public function getContentDefaultImgUrlAttr($value, $data)
    {
        return $this['contentDefaultImg']['file_url'] ?? '';
    }

    // 关联分类默认图片文件
    public function categoryDefaultImg()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'category_default_img_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取分类默认图片链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("category_default_img_url", type="string", desc="分类默认图片链接")
     */
    public function getCategoryDefaultImgUrlAttr($value, $data)
    {
        return $this['categoryDefaultImg']['file_url'] ?? '';
    }

    // 关联标签默认图片文件
    public function tagDefaultImg()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'tag_default_img_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取标签默认图片链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_default_img_url", type="string", desc="标签默认图片链接")
     */
    public function getTagDefaultImgUrlAttr($value, $data)
    {
        return $this['tagDefaultImg']['file_url'] ?? '';
    }
}
