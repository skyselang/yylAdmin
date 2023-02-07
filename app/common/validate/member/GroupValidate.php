<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\member;

use think\Validate;
use app\common\model\member\GroupModel;
use app\common\model\member\AttributesModel;
use app\common\model\member\GroupApisModel;

/**
 * 会员分组验证器
 */
class GroupValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'group_id'   => ['require'],
        'group_name' => ['require', 'checkExisted'],
        'api_ids'    => ['array'],
        'member_ids' => ['array'],
    ];

    // 错误信息
    protected $message = [
        'group_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'         => ['group_id'],
        'add'          => ['group_name'],
        'edit'         => ['group_id', 'group_name'],
        'dele'         => ['ids'],
        'editapi'      => ['ids', 'api_ids'],
        'default'      => ['ids'],
        'disable'      => ['ids'],
        'member'       => ['group_id'],
        'memberRemove' => ['group_id', 'member_ids'],
    ];

    // 验证场景定义：分组删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkApiMember');
    }

    // 自定义验证规则：分组是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new GroupModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['group_name', '=', $data['group_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '名称已存在：' . $data['group_name'];
        }

        return true;
    }

    // 自定义验证规则：分组是否存在接口或会员
    protected function checkApiMember($value, $rule, $data = [])
    {
        // $info = GroupApisModel::where('group_id', 'in', $data['ids'])->find();
        // if ($info) {
        //     return '分组下存在接口，请在[接口]或[修改]中解除后再删除：' . $info['group_id'];
        // }

        // $info = AttributesModel::where('group_id', 'in', $data['ids'])->find();
        // if ($info) {
        //     return '分组下存在会员，请在[会员]中解除后再删除：' . $info['group_id'];
        // }

        return true;
    }
}
