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
use app\common\service\member\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员第三方账号模型
 */
class ThirdModel extends Model
{
    // 表名
    protected $name = 'member_third';
    // 表主键
    protected $pk = 'third_id';

    // 关联会员
    public function member()
    {
        return $this->hasOne(MemberModel::class, 'member_id', 'member_id');
    }
    /**
     * 获取会员昵称
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_nickname", type="string", desc="会员昵称")
     */
    public function getMemberNicknameAttr($value, $data)
    {
        return $this['member']['nickname'] ?? '';
    }
    /**
     * 获取会员用户名
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_username", type="string", desc="会员用户名")
     */
    public function getMemberUsernameAttr($value, $data)
    {
        return $this['member']['username'] ?? '';
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
}
