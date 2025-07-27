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
use app\common\service\system\DeptService as Service;
use app\common\model\system\DeptModel as Model;
use app\common\model\system\UserAttributesModel;

/**
 * 部门管理验证器
 */
class DeptValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'field'     => ['require', 'checkUpdateField'],
        'dept_id'   => ['require'],
        'dept_pid'  => ['checkPid'],
        'dept_name' => ['require', 'checkExisted'],
        'user_ids'  => ['array'],
    ];

    // 错误信息
    protected $message = [
        'dept_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['dept_id'],
        'add'      => ['dept_pid', 'dept_name'],
        'edit'     => ['dept_id', 'dept_pid', 'dept_name'],
        'dele'     => ['ids'],
        'disable'  => ['ids'],
        'update'   => ['ids', 'field'],
        'userList' => ['dept_id'],
        'userLift' => ['dept_id', 'user_ids'],
    ];

    // 验证场景定义：部门删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkUser']);
    }

    // 自定义验证规则：部门是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;
        $id    = $data[$pk] ?? 0;
        $pid   = $data[$pidk] ?? 0;

        $unique = $data['dept_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['dept_unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], [$pidk, '=', $pid], ['dept_name', '=', $data['dept_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['dept_name'];
        }

        return true;
    }

    // 自定义验证规则：部门批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        $model = $this->model();
        $pidk  = $model->pidk;
        if ($edit_field == $pidk) {
            $data[$pidk] = $data['value'];
            return $this->checkPid($value, $rule, $data);
        }

        return true;
    }

    // 自定义验证规则：部门上级
    protected function checkPid($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;

        $ids = $data['ids'] ?? [];
        if ($data[$pk] ?? 0) {
            $ids[] = $data[$pk];
        }

        $list = $this->service::list('list');
        foreach ($ids as $id) {
            if ($data[$pidk] == $id) {
                return lang('上级不能等于自己');
            }
            $cycle = tree_is_cycle($list, $id, $data[$pidk], $pk, $pidk);
            if ($cycle) {
                return lang('不能选择该上级');
            }
        }

        return true;
    }

    // 自定义验证规则：部门是否存在下级
    protected function checkChild($value, $rule, $data = [])
    {
        $model = $this->model();
        $pidk  = $model->pidk;
        $where = where_delete([$pidk, 'in', $data['ids']]);
        $info  = $model->field($pidk)->where($where)->find();
        if ($info) {
            return lang('存在下级，无法删除：') . $info[$pidk];
        }

        return true;
    }

    // 自定义验证规则：部门是否存在用户
    protected function checkUser($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $info  = UserAttributesModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '部门存在用户，请在[用户]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
