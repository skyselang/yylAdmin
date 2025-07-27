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
use app\common\service\system\MenuService as Service;
use app\common\model\system\MenuModel as Model;
use app\common\model\system\RoleMenusModel;

/**
 * 菜单管理验证器
 */
class MenuValidate extends Validate
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
        'menu_id'   => ['require'],
        'menu_pid'  => ['checkPid'],
        'menu_name' => ['require', 'checkExisted'],
        'role_ids'  => ['array'],
    ];

    // 错误信息
    protected $message = [
        'menu_name.require' => '请输入菜单名称',
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['menu_id'],
        'add'         => ['menu_pid', 'menu_name'],
        'edit'        => ['menu_id', 'menu_pid', 'menu_name'],
        'dele'        => ['ids'],
        'update'      => ['ids', 'field'],
        'disable'     => ['ids'],
        'editPid'     => ['ids', 'menu_pid'],
        'editUnlogin' => ['ids'],
        'editUnauth'  => ['ids'],
        'editUnrate'  => ['ids'],
        'roleList'    => ['menu_id'],
        'roleLift'    => ['menu_id', 'role_ids'],
    ];

    // 验证场景定义：菜单删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkRole']);
    }

    // 自定义验证规则：菜单是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;
        $id    = $data[$pk] ?? 0;
        $pid   = $data[$pidk] ?? 0;

        $where = where_delete([[$pk, '<>', $id], [$pidk, '=', $pid], ['menu_name', '=', $data['menu_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('菜单名称已存在：') . $data['menu_name'];
        }

        $menu_url = $data['menu_url'] ?? '';
        if ($menu_url) {
            $where = where_delete([[$pk, '<>', $id], ['menu_url', '=', $menu_url]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('菜单链接已存在：') . $data['menu_url'];
            }
        }

        return true;
    }

    // 自定义验证规则：菜单批量修改字段
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

    // 自定义验证规则：菜单上级
    protected function checkPid($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;

        $ids = $data['ids'] ?? [];
        if ($data[$pk] ?? 0) {
            $ids[] = $data[$pk];
        }

        $list = $this->service::list('list', [where_delete()]);
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

    // 自定义验证规则：菜单是否存在下级
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

    // 自定义验证规则：菜单是否存在角色
    protected function checkRole($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $info  = RoleMenusModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '菜单存在角色，请在[角色]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
