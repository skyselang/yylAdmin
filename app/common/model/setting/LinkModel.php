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
 * 友链管理模型
 */
class LinkModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'setting_link';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'link_id';

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
        return $this['image']['file_url'] ?? '';
    }

    /**
     * 获取是否显示下划线名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("underline_name", type="string", desc="是否显示下划线名称")
     * @return string
     */
    public function getUnderlineNameAttr($value, $data)
    {
        return ($data['underline'] ?? 0) ? '是' : '否';
    }
}
