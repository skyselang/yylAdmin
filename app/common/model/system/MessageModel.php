<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\system;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 消息管理模型
 */
class MessageModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_message';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'message_id';

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
     * 关联用户消息
     * @return \think\model\relation\BelongsToMany
     */
    public function userMessage()
    {
        return $this->belongsToMany(UserModel::class, UserMessageModel::class, 'user_id', 'message_id');
    }

    /**
     * 获取用户id
     * @Apidoc\Field("")
     * @Apidoc\AddField("user_ids", type="array", desc="用户id")
     */
    public function getUserIdsAttr($value, $data)
    {
        return model_relation_fields($this['userMessage'], 'user_id');
    }
}
