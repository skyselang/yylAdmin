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
use app\common\model\member\MemberModel;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 反馈管理模型
 */
class FeedbackModel extends Model
{
    // 表名
    protected $name = 'setting_feedback';
    // 表主键
    protected $pk = 'feedback_id';

    /**
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     */
    public function getTypeNameAttr($value, $data)
    {
        return SettingService::feedbackTypes($data['type']);
    }

    /**
     * 获取状态名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("status_name", type="string", desc="状态名称")
     */
    public function getStatusNameAttr($value, $data)
    {
        return SettingService::feedbackStatuss($data['status']);
    }

    // 关联会员
    public function member()
    {
        return $this->hasOne(MemberModel::class, 'member_id', 'member_id');
    }
    /**
     * 获取会员用户名
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_username", type="string", desc="会员用户名")
     */
    public function getMemberUsernameAttr()
    {
        return $this['member']['username'] ?? '';
    }

    // 关联图片
    public function image()
    {
        return $this->belongsToMany(FileModel::class, SettingFilesModel::class, 'file_id', 'feedback_id')->where(where_disdel());
    }
    // 获取图片
    public function getImagesAttr()
    {
        if ($this['image']) {
            $images = $this['image']->append(['file_url'])->toArray();
        }
        return $images ?? [];
    }
    // 获取图片id
    public function getImageIdsAttr()
    {
        if ($this['image']) {
            $image_ids = array_column($this['image']->append(['file_url'])->toArray(), 'file_id');
        }
        return $image_ids ?? [];
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
