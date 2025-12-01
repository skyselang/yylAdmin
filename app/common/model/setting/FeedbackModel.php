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
use app\common\model\member\MemberModel;
use app\common\service\setting\SettingService;

/**
 * 反馈管理模型
 */
class FeedbackModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'setting_feedback';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'feedback_id';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     * @return string
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? lang('是') : lang('否');
    }

    /**
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     * @return string
     */
    public function getTypeNameAttr($value, $data)
    {
        return SettingService::feedbackTypes($data['type']);
    }

    /**
     * 获取状态名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("status_name", type="string", desc="状态名称")
     * @return string
     */
    public function getStatusNameAttr($value, $data)
    {
        return SettingService::feedbackStatuss($data['status']);
    }

    /**
     * 关联会员
     * @return \think\model\relation\HasOne
     */
    public function member()
    {
        return $this->hasOne(MemberModel::class, 'member_id', 'member_id');
    }
    /**
     * 获取会员昵称
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_nickname", type="string", desc="会员昵称")
     * @return string
     */
    public function getMemberNicknameAttr()
    {
        return $this['member']['nickname'] ?? '';
    }
    /**
     * 获取会员用户名
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_username", type="string", desc="会员用户名")
     * @return string
     */
    public function getMemberUsernameAttr()
    {
        return $this['member']['username'] ?? '';
    }

    /**
     * 关联图片
     * @return \think\model\relation\BelongsToMany
     */
    public function image()
    {
        return $this->belongsToMany(FileModel::class, SettingFilesModel::class, 'file_id', 'feedback_id')->where(where_disdel());
    }
    /**
     * 获取图片
     * @Apidoc\Field("")
     * @Apidoc\AddField("images", type="array", desc="图片")
     * @return array
     */
    public function getImagesAttr()
    {
        if ($this['image']) {
            $images = $this['image']->append(['file_url'])->toArray();
        }
        return $images ?? [];
    }
    /**
     * 获取图片id
     * @Apidoc\Field("")
     * @Apidoc\AddField("image_ids", type="array", desc="图片id")
     * @return array
     */
    public function getImageIdsAttr()
    {
        if ($this['image']) {
            $image_ids = array_column($this['image']->append(['file_url'])->toArray(), 'file_id');
        }
        return $image_ids ?? [];
    }
}
