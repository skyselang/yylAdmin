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
use app\common\service\setting\SettingService;

/**
 * 通告管理模型
 */
class NoticeModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'setting_notice';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'notice_id';

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
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     * @return string
     */
    public function getTypeNameAttr($value, $data)
    {
        return SettingService::noticeTypes($data['type']);
    }
}
