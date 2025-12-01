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
use app\common\service\member\SettingService;
use app\common\service\setting\RegionService;
use think\Lang;

/**
 * 会员管理模型
 */
class MemberModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'member';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'member_id';

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
     * 关联头像
     * @return \think\model\relation\HasOne
     */
    public function avatar()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'avatar_id')->append(['file_url'])->where(where_disdel());
    }
    /**
     * 获取头像链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("avatar_url", type="string", desc="头像链接")
     * @return string
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

    /**
     * 获取性别名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("gender_name", type="string", desc="性别名称")
     * @return string
     */
    public function getGenderNameAttr($value, $data)
    {
        return SettingService::genders($data['gender']);
    }

    /**
     * 获取年龄
     * @Apidoc\Field("")
     * @Apidoc\AddField("age", type="int", desc="年龄")
     * @return int
     */
    public function getAgeAttr($value, $data)
    {
        if ($data['birthday'] ?? '') {
            return date('Y') - date('Y', strtotime($data['birthday']));
        }
        return '';
    }

    /**
     * 获取家乡名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("hometown_name", type="string", desc="家乡名称")
     * @return string
     */
    public function getHometownNameAttr($value, $data)
    {
        return RegionService::info($data['hometown_id'], false)['region_fullname'] ?? '';
    }

    /**
     * 获取所在地名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("region_name", type="string", desc="所在地名称")
     * @return string
     */
    public function getRegionNameAttr($value, $data)
    {
        return RegionService::info($data['region_id'], false)['region_fullname'] ?? '';
    }

    /**
     * 获取平台名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("platform_name", type="string", desc="平台名称")
     * @return string
     */
    public function getPlatformNameAttr($value, $data)
    {
        return SettingService::platforms($data['platform']);
    }

    /**
     * 获取应用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("application_name", type="string", desc="应用名称")
     * @return string
     */
    public function getApplicationNameAttr($value, $data)
    {
        return SettingService::applications($data['application']);
    }

    /**
     * 关联标签
     * @return \think\model\relation\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(TagModel::class, AttributesModel::class, 'tag_id', 'member_id');
    }
    /**
     * 获取标签id
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_ids", type="array", desc="标签id", mock="@natural(1,50)")
     * @return array
     */
    public function getTagIdsAttr()
    {
        return model_relation_fields($this['tags'], 'tag_id');
    }
    /**
     * 获取标签名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("tag_names", type="string", desc="标签名称")
     * @return string
     */
    public function getTagNamesAttr()
    {
        return model_relation_fields($this['tags'], 'tag_name', true);
    }

    /**
     * 关联分组
     * @return \think\model\relation\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(GroupModel::class, AttributesModel::class, 'group_id', 'member_id');
    }
    /**
     * 获取分组id
     * @Apidoc\Field("")
     * @Apidoc\AddField("group_ids", type="array", desc="分组id", mock="@natural(1,50)")
     * @return array
     */
    public function getGroupIdsAttr()
    {
        return model_relation_fields($this['groups'], 'group_id');
    }
    /**
     * 获取分组名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("group_names", type="string", desc="分组名称")
     * @return string
     */
    public function getGroupNamesAttr()
    {
        return model_relation_fields($this['groups'], 'group_name', true);
    }

    /**
     * 关联第三方账号
     * @return \think\model\relation\HasMany
     */
    public function thirds()
    {
        return $this->hasMany(ThirdModel::class, 'third_id', 'member_id');
    }
    /**
     * 获取第三方账号id
     * @Apidoc\Field("")
     * @Apidoc\AddField("third_ids", type="array", desc="第三方账号id")
     * @return array
     */
    public function getThirdIdsAttr()
    {
        return model_relation_fields($this['thirds'], 'third_id');
    }
    /**
     * 获取第三方账号昵称
     * @Apidoc\Field("")
     * @Apidoc\AddField("nicknames", type="string", desc="第三方账号昵称")
     * @return string
     */
    public function getThirdNicknamesAttr()
    {
        return model_relation_fields($this['thirds'], 'nickname', true);
    }

    /**
     * 获取是否超会名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_super_name", type="string", desc="是否超会名称")
     * @return string
     */
    public function getIsSuperNameAttr($value, $data)
    {
        return ($data['is_super'] ?? 0) ? lang('是') : lang('否');
    }
}
