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
use app\common\service\member\SettingService;

/**
 * 会员日志模型
 */
class LogModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'member_log';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'log_id';

    /**
     * 关联会员
     * @Apidoc\Field("")
     * @Apidoc\AddField("member_nickname", type="string", desc="会员昵称")
     * @Apidoc\AddField("member_username", type="string", desc="会员用户名")
     * @return \think\model\relation\HasOne
     */
    public function member()
    {
        return $this->hasOne(MemberModel::class, 'member_id', 'member_id');
    }
    /**
     * 获取会员昵称
     * 
     * @param mixed $value 数据
     * @return string
     */
    public function getMemberNicknameAttr($value)
    {
        return $this['member']['nickname'] ?? '';
    }
    /**
     * 获取会员用户名
     * @param mixed $value 数据
     * @return string
     */
    public function getMemberUsernameAttr($value)
    {
        return $this['member']['username'] ?? '';
    }

    /**
     * 关联会员接口
     * @Apidoc\Field("")
     * @Apidoc\AddField("api_name", type="string", desc="接口名称")
     * @Apidoc\AddField("api_url", type="string", desc="接口链接")
     * @return \think\model\relation\HasOne
     */
    public function api()
    {
        return $this->hasOne(ApiModel::class, 'api_id', 'api_id');
    }
    /**
     * 获取会员接口名称
     * @param mixed $value 数据
     * @param mixed $data 数据
     * @return string
     */
    public function getApiNameAttr($value, $data)
    {
        $api_name = $this['api']['api_name'] ?? '';
        if ($api_name) {
            return $api_name;
        }
        return $data['api_name'] ?? '';
    }
    /**
     * 获取会员接口链接
     * @param mixed $value 数据
     * @param mixed $data 数据
     * @return string
     */
    public function getApiUrlAttr($value, $data)
    {
        return $this['api']['api_url'] ?? $data['request_url'] ?? '';
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
     * 修改请求参数
     * @param mixed $value 数据
     * @return string
     */
    public function setRequestParamAttr($value)
    {
        return json_encode($value);
    }
    /**
     * 获取请求参数
     * @param mixed $value 数据
     * @return mixed
     */
    public function getRequestParamAttr($value)
    {
        if (empty($value)) {
            $value = '{}';
        }
        return json_decode($value, true);
    }
}
