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
use hg\apidoc\annotation as Apidoc;

/**
 * 轮播管理模型
 */
class CarouselModel extends Model
{
    // 表名
    protected $name = 'setting_carousel';
    // 表主键
    protected $pk = 'carousel_id';

    /**
     * 关联文件
     * @Apidoc\Field("")
     * @Apidoc\AddField("file_url", type="string", desc="文件链接")
     * @Apidoc\AddField("file_name", type="string", desc="文件名称")
     * @Apidoc\AddField("file_ext", type="string", desc="文件后缀")
     * @Apidoc\AddField("file_type", type="string", desc="文件类型")
     * @Apidoc\AddField("file_type_name", type="string", desc="文件类型名称")
     */
    public function file()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'file_id')->append(['file_url', 'file_type_name'])->where(where_disdel());
    }
    /**
     * 获取文件链接
     */
    public function getFileUrlAttr()
    {
        return $this['file']['file_url'] ?? '';
    }
    /**
     * 获取文件名称
     */
    public function getFileNameAttr()
    {
        return $this['file']['file_name'] ?? '';
    }
    /**
     * 获取文件后缀
     */
    public function getFileExtAttr()
    {
        return $this['file']['file_ext'] ?? '';
    }
    /**
     * 获取文件类型
     */
    public function getFileTypeAttr($value, $data)
    {
        return $this['file']['file_type'] ?? '';
    }
    /**
     * 获取文件类型名称
     */
    public function getFileTypeNameAttr($value, $data)
    {
        return $this['file']['file_type_name'] ?? '';
    }

    // 关联文件列表
    public function files()
    {
        return $this->belongsToMany(FileModel::class, SettingFilesModel::class, 'file_id', 'carousel_id')->where(where_disdel());
    }
    // 获取文件列表
    public function getFileListAttr()
    {
        if ($this['files']) {
            $files = $this['files']->append(['file_url'])->toArray();
        }
        return $files ?? [];
    }
    // 获取文件列表id
    public function getFileListIdsAttr()
    {
        if ($this['files']) {
            $file_ids = array_column($this['files']->append(['file_url'])->toArray(), 'file_id');
        }
        return $file_ids ?? [];
    }

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }
}
