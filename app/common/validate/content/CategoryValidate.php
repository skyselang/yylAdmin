<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\content;

use think\Validate;
use app\common\service\content\CategoryService as Service;
use app\common\model\content\CategoryModel as Model;
use app\common\model\content\AttributesModel;

/**
 * 内容分类验证器
 */
class CategoryValidate extends Validate
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
        'ids'           => ['require', 'array'],
        'field'         => ['require', 'checkUpdateField'],
        'category_id'   => ['require'],
        'category_pid'  => ['checkPid'],
        'category_name' => ['require', 'checkExisted'],
        'import_file'   => ['require', 'file', 'fileExt' => 'xlsx'],
        'content_ids'   => ['array'],
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'category_name.require' => '请输入名称',
        'import_file.require'   => '请选择导入文件',
        'import_file.fileExt'   => '只允许xlsx文件格式',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'info'        => ['category_id'],
        'add'         => ['category_pid', 'category_name'],
        'edit'        => ['category_id', 'category_pid', 'category_name'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'update'      => ['ids', 'field'],
        'import'      => ['import_file'],
        'contentList' => ['category_id'],
        'contentLift' => ['category_id', 'content_ids'],
    ];

    /**
     * 验证场景定义：分类删除
     */
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkContent']);
    }

    /**
     * 自定义验证规则：分类是否已存在
     */
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $pidk  = $model->pidk;
        $id    = $data[$pk] ?? 0;
        $pid   = $data[$pidk] ?? 0;

        $unique = $data['category_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['category_unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], [$pidk, '=', $pid], ['category_name', '=', $data['category_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['category_name'];
        }

        return true;
    }

    /**
     * 自定义验证规则：分类批量修改字段
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
     * 自定义验证规则：分类上级
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
     * 自定义验证规则：分类是否存在下级
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
     * 自定义验证规则：分类是否存在内容
     */
    protected function checkContent($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $info  = AttributesModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '分类存在内容，请在[内容]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
