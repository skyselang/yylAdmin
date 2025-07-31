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
use hg\apidoc\annotation as Apidoc;
use app\common\service\file\SettingService;

/**
 * 文件管理模型
 */
class FileModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'file';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'file_id';

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
     * 获取储存名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("storage_name", type="string", desc="储存名称")
     * @return string
     */
    public function getStorageNameAttr($value, $data)
    {
        return SettingService::storages($data['storage']);
    }

    /**
     * 关联文件分组
     * @return \think\model\relation\HasOne
     */
    public function group()
    {
        return $this->hasOne(GroupModel::class, 'group_id', 'group_id')->where([where_delete()]);
    }
    /**
     * 获取文件分组名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("group_name", type="string", desc="分组名称")
     * @return string
     */
    public function getGroupNameAttr($value, $data)
    {
        return $this['group']['group_name'] ?? '';
    }

    /**
     * 关联标签
     * @return \think\model\relation\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, TagsModel::class, 'tag_id', 'file_id');
    }
    /**
     * 获取标签id
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_ids", type="array", desc="标签id", mock="@natural(1,50)")
     * @return string|array
     */
    public function getTagIdsAttr()
    {
        return model_relation_fields($this['tags'], 'tag_id');
    }
    /**
     * 获取标签名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_names", type="string", desc="标签名称")
     * @return string|array
     */
    public function getTagNamesAttr()
    {
        return model_relation_fields($this['tags'], 'tag_name', true);
    }

    /**
     * 获取文件类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_type_name", type="string", desc="文件类型名称")
     * @return string
     */
    public function getFileTypeNameAttr($value, $data)
    {
        return SettingService::fileTypes($data['file_type']);
    }

    /**
     * 获取文件大小名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_size_name", type="string", desc="文件大小名称")
     * @return string
     */
    public function getFileSizeNameAttr($value, $data)
    {
        return SettingService::fileSize($data['file_size']);
    }

    /**
     * 获取文件链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_url", type="string", desc="文件链接")
     * @return string
     */
    public function getFileUrlAttr($value, $data)
    {
        return SettingService::fileUrl($data);
    }
}
