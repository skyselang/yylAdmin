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
use app\common\service\system\PostService as Service;
use app\common\model\system\PostModel as Model;
use app\common\model\system\UserAttributesModel;

/**
 * 职位管理验证器
 */
class PostValidate extends Validate
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

    /**
     * 验证规则
     */
    protected $rule = [
        'ids'       => ['require', 'array'],
        'field'     => ['require', 'checkUpdateField'],
        'post_id'   => ['require'],
        'post_pid'  => ['checkPid'],
        'post_name' => ['require', 'checkExisted'],
        'user_ids'  => ['array'],
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'post_name.require' => '请输入名称',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'info'     => ['post_id'],
        'add'      => ['post_pid', 'post_name'],
        'edit'     => ['post_id', 'post_pid', 'post_name'],
        'dele'     => ['ids'],
        'disable'  => ['ids'],
        'update'   => ['ids', 'field'],
        'userList' => ['post_id'],
        'userLift' => ['post_id', 'user_ids'],
    ];

    /**
     * 验证场景定义：职位删除
     */
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkUser']);
    }

    /**
     * 自定义验证规则：职位是否已存在
     */
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;
        $id    = $data[$pk] ?? 0;
        $pid   = $data[$pidk] ?? 0;

        $unique = $data['post_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['post_unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], [$pidk, '=', $pid], ['post_name', '=', $data['post_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['post_name'];
        }

        return true;
    }

    /**
     * 自定义验证规则：职位批量修改字段
     */
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

    /**
     * 自定义验证规则：职位上级
     */
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

    /**
     * 自定义验证规则：职位是否存在下级
     */
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

    /**
     * 自定义验证规则：职位是否存在用户
     */
    protected function checkUser($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $info  = UserAttributesModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '职位存在用户，请在[用户]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
