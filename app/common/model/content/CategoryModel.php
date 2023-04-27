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
use app\common\model\file\FileModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 内容分类模型
 */
class CategoryModel extends Model
{
    // 表名
    protected $name = 'content_category';
    // 表主键
    protected $pk = 'category_id';

    // 关联封面
    public function cover()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'cover_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取封面链接
     * @Apidoc\field("")
     * @Apidoc\AddField("cover_url", type="string", desc="封面链接")
     */
    public function getCoverUrlAttr()
    {
        return $this['cover']['file_url'] ?? '';
    }

    // 关联图片
    public function images()
    {
        return $this->belongsToMany(FileModel::class, AttributesModel::class, 'file_id', 'category_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取图片id
    public function getImageIdsAttr()
    {
        if ($this['images']) {
            $images = $this['images']->toArray();
            foreach ($images as $image) {
                $image_ids[] = $image['file_id'];
            }
        }
        return $image_ids ?? [];
    }
}
