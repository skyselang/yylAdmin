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
use app\common\service\content\SettingService;
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

    // 关联图片
    public function image()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'image_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取图片链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("image_url", type="string", desc="图片链接")
     */
    public function getImageUrlAttr($value, $data)
    {
        $file_url = $this['image']['file_url'] ?? '';
        if (empty($file_url)) {
            $setting = SettingService::info();
            if ($setting['content_default_img_open']) {
                $file_url = $setting['content_default_img_url'] ?? '';
            }
        }
        return $file_url;
    }

    // 关联分类
    public function categorys()
    {
        return $this->belongsToMany(CategoryModel::class, AttributesModel::class, 'category_id', 'content_id');
    }
    /**
     * 获取分类id
     * @Apidoc\Field("")
     * @Apidoc\AddField("category_ids", type="array", desc="分类id")
     */
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
    /**
     * 获取标签id
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_ids", type="array", desc="标签id")
     */
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
        return $this->belongsToMany(FileModel::class, AttributesModel::class, 'file_id', 'content_id')->where(where_disdel());
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
    // 获取图片id
    public function getImageIdsAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'image') {
                    $image_ids[] = $file['file_id'];
                }
            }
        }
        return $image_ids ?? [];
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
    // 获取视频id
    public function getVideoIdsAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'video') {
                    $video_ids[] = $file['file_id'];
                }
            }
        }
        return $video_ids ?? [];
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
    // 获取音频id
    public function getAudioIdsAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'audio') {
                    $audio_ids[] = $file['file_id'];
                }
            }
        }
        return $audio_ids ?? [];
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
    // 获取文档id
    public function getWordIdsAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'word') {
                    $word_ids[] = $file['file_id'];
                }
            }
        }
        return $word_ids ?? [];
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
    // 获取其它id
    public function getOtherIdsAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
            foreach ($files as $file) {
                if ($file['pivot']['file_type'] == 'other') {
                    $other_ids[] = $file['file_id'];
                }
            }
        }
        return $other_ids ?? [];
    }

    // 获取展示点击量
    public function getHitsShowAttr($value, $data)
    {
        // 初始点击量+真实点击量
        return ($data['hits_initial'] ?? 0) + ($data['hits'] ?? 0);
    }
}
