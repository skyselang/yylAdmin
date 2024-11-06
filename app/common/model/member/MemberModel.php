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
use app\common\model\file\FileModel;
use app\common\service\member\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员管理模型
 */
class MemberModel extends Model
{
    // 表名
    protected $name = 'member';
    // 表主键
    protected $pk = 'member_id';

    /**
     * 获取性别名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("gender_name", type="string", desc="性别名称")
     */
    public function getGenderNameAttr($value, $data)
    {
        return SettingService::genders($data['gender']);
    }

    /**
     * 获取平台名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("platform_name", type="string", desc="平台名称")
     */
    public function getPlatformNameAttr($value, $data)
    {
        return SettingService::platforms($data['platform']);
    }

    /**
     * 获取应用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("application_name", type="string", desc="应用名称")
     */
    public function getApplicationNameAttr($value, $data)
    {
        return SettingService::applications($data['application']);
    }

    // 关联头像
    public function avatar()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'avatar_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取头像链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("avatar_url", type="string", desc="头像链接")
     */
    public function getAvatarUrlAttr($value, $data)
    {
        // 上传头像
        if ($data['avatar_id'] ?? '') {
            return $this['avatar']['file_url'] ?? '';
        }
        // 第三方头像
        if ($data['headimgurl'] ?? '') {
            return $data['headimgurl'];
        }
        // 默认头像
        $setting = SettingService::info();
        return $setting['default_avatar_url'] ?? '';
    }

    // 关联标签
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, AttributesModel::class, 'tag_id', 'member_id');
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

    // 关联分组
    public function groups()
    {
        return $this->belongsToMany(GroupModel::class, AttributesModel::class, 'group_id', 'member_id');
    }
    /**
     * 获取分组id
     * @Apidoc\Field("")
     * @Apidoc\AddField("group_ids", type="array", desc="分组id")
     */
    public function getGroupIdsAttr()
    {
        return relation_fields($this['groups'], 'group_id');
    }
    /**
     * 获取分组名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("group_names", type="string", desc="分组名称")
     */
    public function getGroupNamesAttr()
    {
        return relation_fields($this['groups'], 'group_name', true);
    }

    // 关联第三方账号
    public function thirds()
    {
        return $this->hasMany(ThirdModel::class, 'third_id', 'member_id');
    }
    /**
     * 获取第三方账号id
     * @Apidoc\Field("")
     * @Apidoc\AddField("third_ids", type="array", desc="第三方账号id")
     */
    public function getThirdIdsAttr()
    {
        return relation_fields($this['thirds'], 'third_id');
    }
    /**
     * 获取第三方账号昵称
     * @Apidoc\Field("")
     * @Apidoc\AddField("nicknames", type="string", desc="第三方账号昵称")
     */
    public function getThirdNicknamesAttr()
    {
        return relation_fields($this['thirds'], 'nickname', true);
    }

    /**
     * 获取是否超会名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_super_name", type="string", desc="是否超会名称")
     */
    public function getIsSuperNameAttr($value, $data)
    {
        return ($data['is_super'] ?? 0) ? '是' : '否';
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
