<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\system;

use think\model\Pivot;
use hg\apidoc\annotation as Apidoc;

/**
 * 用户消息模型
 */
class UserMessageModel extends Pivot
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_user_message';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'user_message_id';
    /**
     * 自动写入时间
     * @var string
     */
    protected $autoWriteTimestamp = 'datetime';
    /**
     * 定义添加时间戳字段名
     * @var string
     */
    protected $createTime = 'create_time';
    /**
     * 关闭自动写入update_time字段
     * @var string
     */
    protected $updateTime = false;


    /**
     * 关联用户
     * @Apidoc\Field("")
     * @Apidoc\AddField("user_nickname", type="string", desc="用户昵称")
     * @Apidoc\AddField("user_username", type="string", desc="用户账号")
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(UserModel::class, 'user_id', 'user_id')->bind(['nickname' => 'user_nickname', 'username' => 'user_username']);
    }

    /**
     * 关联消息
     * @Apidoc\Field("")
     * @Apidoc\AddField("message_title", type="string", desc="消息标题")
     * @Apidoc\AddField("message_content", type="string", desc="消息内容")
     * @return \think\model\relation\HasOne
     */
    public function message()
    {
        return $this->hasOne(MessageModel::class, 'message_id', 'message_id')->bind(['title' => 'message_title']);
    }

    /**
     * 获取是否已读名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_read_name", type="string", desc="是否已读名称")
     * @return string
     */
    public function getIsReadNameAttr($value, $data)
    {
        return ($data['is_read'] ?? 0) ? '是' : '否';
    }

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
}
