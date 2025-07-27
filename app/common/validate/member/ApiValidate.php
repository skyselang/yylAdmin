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
use app\common\service\member\ApiService as Service;
use app\common\model\member\ApiModel as Model;
use app\common\model\member\GroupApisModel;

/**
 * 会员接口验证器
 */
class ApiValidate extends Validate
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
        'ids'         => ['require', 'array'],
        'field'       => ['require', 'checkUpdateField'],
        'api_id'      => ['require'],
        'api_pid'     => ['checkPid'],
        'api_name'    => ['require', 'checkExisted'],
        'import_file' => ['require', 'file', 'fileExt' => 'xlsx'],
        'group_ids'   => ['array'],
    ];

    // 错误信息
    protected $message = [
        'api_name.require'    => '请输入名称',
        'import_file.require' => '请选择导入文件',
        'import_file.fileExt' => '只允许xlsx文件格式',
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['api_id'],
        'add'         => ['api_pid', 'api_name'],
        'edit'        => ['api_id', 'api_pid', 'api_name'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'update'      => ['ids', 'field'],
        'editPid'     => ['ids', 'api_pid'],
        'editUnlogin' => ['ids'],
        'editUnauth'  => ['ids'],
        'editUnrate'  => ['ids'],
        'import'      => ['import_file'],
        'groupList'   => ['api_id'],
        'groupLift'   => ['api_id', 'group_ids'],
    ];

    // 验证场景定义：接口删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkGroup']);
    }

    // 自定义验证规则：接口是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;
        $id    = $data[$pk] ?? 0;
        $pid   = $data[$pidk] ?? 0;

        $where = where_delete([[$pk, '<>', $id], [$pidk, '=', $pid], ['api_name', '=', $data['api_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['api_name'];
        }

        $url = $data['api_url'] ?? '';
        if ($url) {
            $where = where_delete([[$pk, '<>', $id], ['api_url', '=', $url]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('接口链接已存在：') . $url;
            }
        }

        return true;
    }

    // 自定义验证规则：接口批量修改字段
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

    // 自定义验证规则：接口上级
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

    // 自定义验证规则：接口是否存在下级
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

    // 自定义验证规则：接口是否存在分组
    protected function checkGroup($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $info  = GroupApisModel::field($pk)->where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '接口存在分组，请在[分组]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
