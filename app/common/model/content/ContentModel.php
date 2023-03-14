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
 * 内容管理模型
 */
class ContentModel extends Model
{
    // 表名
    protected $name = 'content';
    // 表主键
    protected $pk = 'content_id';

    // 关联封面
    public function cover()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'cover_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取封面链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("cover_url", type="string", desc="封面链接")
     */
    public function getCoverUrlAttr($value, $data)
    {
        return $this['cover']['file_url'] ?? '';
    }

    // 关联分类
    public function categorys()
    {
        return $this->belongsToMany(CategoryModel::class, AttributesModel::class, 'category_id', 'content_id');
    }
    // 获取分类id
    public function getCategoryIdsAttr()
    {
        return relation_fields($this['categorys'], 'category_id');
    }
    /**
     * 获取分类名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("category_names", type="string", desc="分类名称")
     */
    public function getCategoryNamesAttr()
    {
        return relation_fields($this['categorys'], 'category_name', true);
    }

    // 关联标签
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, AttributesModel::class, 'tag_id', 'content_id');
    }
    // 获取标签id
    public function getTagIdsAttr()
    {
        return relation_fields($this['tags'], 'tag_id');
    }
    /**
     * 获取标签名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_names", type="string", desc="标签名称")
     */
    public function getTagNamesAttr()
    {
        return relation_fields($this['tags'], 'tag_name', true);
    }

    // 关联文件
    public function files()
    {
        return $this->belongsToMany(FileModel::class, FilesModel::class, 'file_id', 'content_id')->where(where_disdel());
    }
    // 获取图片文件
    public function getImagesAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'image') {
                    $images[] = $file;
                }
            }
        }
        return $images ?? [];
    }
    // 获取视频文件
    public function getVideosAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'video') {
                    $videos[] = $file;
                }
            }
        }
        return $videos ?? [];
    }
    // 获取音频文件
    public function getAudiosAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'audio') {
                    $audios[] = $file;
                }
            }
        }
        return $audios ?? [];
    }
    // 获取文档文件
    public function getWordsAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'word') {
                    $words[] = $file;
                }
            }
        }
        return $words ?? [];
    }
    // 获取其它文件
    public function getOthersAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'other') {
                    $others[] = $file;
                }
            }
        }
        return $others ?? [];
    }
}
