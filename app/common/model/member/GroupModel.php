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
    /**
     * 表名
     * @var string
     */
    protected $name = 'member_group';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'group_id';
    /**
     * 名称字段
     * @var string
     */
    public $namek = 'group_name';

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
     * 关联接口
     * @return \think\model\relation\BelongsToMany
     */
    public function apis()
    {
        return $this->belongsToMany(ApiModel::class, GroupApisModel::class, 'api_id', 'group_id');
    }
    /**
     * 获取接口id
     * @Apidoc\Field("")
     * @Apidoc\AddField("api_ids", type="array", desc="接口id")
     * @return array
     */
    public function getApiIdsAttr()
    {
        return model_relation_fields($this['apis'], 'api_id');
    }

    /**
     * 获取是否默认名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_default_name", type="string", desc="是否默认名称")
     * @return string
     */
    public function getIsDefaultNameAttr($value, $data)
    {
        return ($data['is_default'] ?? 0) ? lang('是') : lang('否');
    }
}
