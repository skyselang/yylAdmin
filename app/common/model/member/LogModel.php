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
 * 会员日志模型
 */
class LogModel extends Model
{
    // 表名
    protected $name = 'member_log';
    // 表主键
    protected $pk = 'log_id';

    // 修改请求参数
    public function setRequestParamAttr($value)
    {
        return serialize($value);
    }
    // 获取请求参数
    public function getRequestParamAttr($value)
    {
        return unserialize($value);
    }

    // 关联会员
    public function member()
    {
        return $this->hasOne(MemberModel::class, 'member_id', 'member_id');
    }
    // 获取会员昵称
    public function getNicknameAttr($value)
    {
        return $this['member']['nickname'] ?? '';
    }
    // 获取会员用户名
    public function getUsernameAttr($value)
    {
        return $this['member']['username'] ?? '';
    }

    // 关联会员接口
    public function api()
    {
        return $this->hasOne(ApiModel::class, 'api_id', 'api_id');
    }
    // 获取会员接口链接
    public function getApiUrlAttr($value, $data)
    {
        return $this['api']['api_url'] ?? $data['request_url'] ?? '';
    }
    // 获取会员接口名称
    public function getApiNameAttr($value)
    {
        return $this['api']['api_name'] ?? '';
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
