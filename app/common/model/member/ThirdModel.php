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

/**
 * 会员第三方账号模型
 */
class ThirdModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'member_third';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'third_id';

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
    // 获取头像
    public function getHeadimgurlAttr($value, $data)
    {
        if ($data['headimgurl'] ?? '') {
            return $data['headimgurl'];
        }
        return $this['avatar']['file_url'] ?? '';
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
    public function getMemberNicknameAttr($value, $data)
    {
        return $this['member']['nickname'] ?? '';
    }
    /**
     * 获取会员用户名
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_username", type="string", desc="会员用户名")
     * @return string
     */
    public function getMemberUsernameAttr($value, $data)
    {
        return $this['member']['username'] ?? '';
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
}
