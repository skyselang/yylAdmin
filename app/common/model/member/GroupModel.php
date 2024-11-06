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

/**
 * 会员分组模型
 */
class GroupModel extends Model
{
    // 表名
    protected $name = 'member_group';
    // 表主键
    protected $pk = 'group_id';

    // 关联接口
    public function apis()
    {
        return $this->belongsToMany(ApiModel::class, GroupApisModel::class, 'api_id', 'group_id');
    }
    /**
     * 获取接口id
     * @Apidoc\Field("")
     * @Apidoc\AddField("api_ids", type="array", desc="接口id")
     */
    public function getApiIdsAttr()
    {
        return relation_fields($this['apis'], 'api_id');
    }

    /**
     * 获取是否默认名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_default_name", type="string", desc="是否默认名称")
     */
    public function getIsDefaultNameAttr($value, $data)
    {
        return ($data['is_default'] ?? 0) ? '是' : '否';
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
