<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\file;

use think\Model;
use app\common\service\file\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 文件管理模型
 */
class FileModel extends Model
{
    // 表名
    protected $name = 'file';
    // 表主键
    protected $pk = 'file_id';

    // 关联文件分组
    public function group()
    {
        return $this->hasOne(GroupModel::class, 'group_id', 'group_id')->where([where_delete()]);
    }
    /**
     * 获取文件分组名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("group_name", type="string", desc="分组名称")
     */
    public function getGroupNameAttr($value, $data)
    {
        return $this['group']['group_name'] ?? '';
    }

    // 关联标签
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, TagsModel::class, 'tag_id', 'file_id');
    }
    // 获取标签id
    public function getTagIdsAttr()
    {
        return relation_fields($this['tags'], 'tag_id');
    }
    /**
     * 获取标签名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_name", type="string", desc="标签名称")
     */
    public function getTagNamesAttr()
    {
        return relation_fields($this['tags'], 'tag_name', true);
    }

    /**
     * 获取文件类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_type_name", type="string", desc="文件类型名称")
     */
    public function getFileTypeNameAttr($value, $data)
    {
        return SettingService::fileTypes($data['file_type']);
    }

    /**
     * 获取文件大小
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_size", type="string", desc="文件大小")
     */
    public function getFileSizeAttr($value, $data)
    {
        return SettingService::fileSize($data['file_size']);
    }

    /**
     * 获取文件链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_url", type="string", desc="文件链接")
     */
    public function getFileUrlAttr($value, $data)
    {
        return SettingService::fileUrl($data);
    }
}
