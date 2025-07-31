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
use app\common\service\content\SettingService;

/**
 * 内容标签模型
 */
class TagModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'content_tag';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'tag_id';
    /**
     * 名称字段
     * @var string
     */
    public $namek = 'tag_name';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     * @return string
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }

    /**
     * 关联图片
     * @return \think\model\relation\HasOne
     */
    public function image()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'image_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取图片链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("image_url", type="string", desc="图片链接")
     * @return string
     */
    public function getImageUrlAttr($value, $data)
    {
        $file_url = $this['image']['file_url'] ?? '';
        if (empty($file_url)) {
            $setting = SettingService::info();
            if ($setting['tag_default_img_open']) {
                $file_url = $setting['tag_default_img_url'] ?? '';
            }
        }
        return $file_url;
    }

    /**
     * 关联文件列表
     * @return \think\model\relation\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(FileModel::class, AttributesModel::class, 'file_id', 'tag_id');
    }
    /**
     * 获取图片列表
     * @Apidoc\Field("")
     * @Apidoc\AddField("images", type="array", desc="图片列表")
     * @return array
     */
    public function getImagesAttr()
    {
        return model_relation_fields($this['files']->append(['file_url']), '');
    }
    /**
     * 获取图片id数组
     * @Apidoc\Field("")
     * @Apidoc\AddField("image_ids", type="array", desc="图片id数组")
     * @return array
     */
    public function getImageIdsAttr()
    {
        return model_relation_fields($this['files']->append(['file_url']), 'file_id');
    }
    /**
     * 获取图片url数组
     * @Apidoc\Field("")
     * @Apidoc\AddField("image_urls", type="array", desc="图片url数组")
     * @return array
     */
    public function getImageUrlsAttr()
    {
        return model_relation_fields($this['files']->append(['file_url']), 'file_url');
    }
}
