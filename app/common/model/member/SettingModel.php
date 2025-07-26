<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;
use app\common\model\file\FileModel;

/**
 * 会员设置模型
 */
class SettingModel extends Model
{
    // 表名
    protected $name = 'member_setting';
    // 表主键
    protected $pk = 'setting_id';

    // 关联会员默认头像文件
    public function defaultavatar()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'default_avatar_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取会员默认头像链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("default_avatar_url", type="string", desc="会员默认头像链接")
     */
    public function getDefaultAvatarUrlAttr()
    {
        return $this['defaultavatar']['file_url'] ?? '';
    }

    // 修改日志请求参数排除字段
    public function setLogParamWithoutAttr($value)
    {
        $value = trim(str_replace('，', ',', $value), ',');
        return $value;
    }
}
