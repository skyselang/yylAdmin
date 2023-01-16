<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\system;

use think\Validate;
use app\common\model\system\DeptModel;
use app\common\model\system\UserAttributesModel;

/**
 * 部门管理验证器
 */
class DeptValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'dept_id'    => ['require'],
        'dept_pid'   => ['checkDeptPid'],
        'dept_name'  => ['require', 'checkExisted'],
        'is_disable' => ['require', 'in' => '0,1'],
        'user_ids'   => ['array'],
    ];

    // 错误信息
    protected $message = [
        'dept_name.require' => '请输入名称',
        'is_disable.in'     => '是否禁用，1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['dept_id'],
        'add'         => ['dept_name'],
        'edit'        => ['dept_id', 'dept_pid', 'dept_name'],
        'dele'        => ['ids'],
        'editpid'     => ['ids', 'dept_pid'],
        'disable'     => ['ids', 'is_disable'],
        'user'        => ['dept_id'],
        'userRemove'  => ['dept_id', 'user_ids'],
        'recycleReco' => ['ids'],
        'recycleDele' => ['ids'],
    ];

    // 验证场景定义：部门删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkUser']);
    }

    // 自定义验证规则：部门上级
    protected function checkDeptPid($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        if ($data['dept_id'] ?? 0) {
            $ids[] = $data['dept_id'];
        }

        foreach ($ids as $id) {
            if ($data['dept_pid'] == $id) {
                return '部门上级不能等于部门本身';
            }
        }

        return true;
    }

    // 自定义验证规则：部门是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new DeptModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;
        $pid = $data['dept_pid'] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['dept_pid', '=', $pid];
        $where[] = ['dept_name', '=', $data['dept_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '名称已存在：' . $data['dept_name'];
        }

        return true;
    }

    // 自定义验证规则：部门是否存在下级部门
    protected function checkChild($value, $rule, $data = [])
    {
        $where = where_delete(['dept_pid', 'in', $data['ids']]);
        $info = DeptModel::field('dept_pid')->where($where)->find();
        if ($info) {
            return '部门存在下级部门，无法删除：' . $info['dept_pid'];
        }

        return true;
    }

    // 自定义验证规则：部门是否存在用户
    protected function checkUser($value, $rule, $data = [])
    {
        $info = UserAttributesModel::where('dept_id', 'in', $data['ids'])->find();
        if ($info) {
            return '部门存在用户，请在[用户]中解除后再删除：' . $info['dept_id'];
        }

        return true;
    }
}
