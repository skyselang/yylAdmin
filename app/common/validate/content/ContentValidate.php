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
use app\common\service\content\ContentService as Service;
use app\common\model\content\ContentModel as Model;
use app\common\model\content\AttributesModel;

/**
 * 内容管理验证器
 */
class ContentValidate extends Validate
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
        'ids'         => ['require', 'array'],
        'field'       => ['require', 'checkUpdateField'],
        'content_id'  => ['require'],
        'name'        => ['require', 'checkExisted'],
        'import_file' => ['require', 'file', 'fileExt' => 'xlsx'],
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'name.require'        => '请输入名称',
        'import_file.require' => '请选择导入文件',
        'import_file.fileExt' => '只允许xlsx文件格式',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'info'    => ['content_id'],
        'add'     => ['name'],
        'edit'    => ['content_id', 'name'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
        'update'  => ['ids', 'field'],
        'import'  => ['import_file'],
    ];

    /**
     * 验证场景定义：后台删除
     */
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkCategoryTag');
    }

    /**
     * 自定义验证规则：内容是否已存在
     */
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        return true;
    }

    /**
     * 自定义验证规则：内容批量修改字段
     */
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    /**
     * 自定义验证规则：内容是否存在分类或标签
     */
    protected function checkCategoryTag($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();

        $info = AttributesModel::field($pk)->whereIn($pk, $data['ids'])->where('category_id', '>', 0)->find();
        if ($info) {
            // return '内容存在分类，请在[分类]或[修改]解除后再删除：' . $info[$pk];
        }

        $info = AttributesModel::field($pk)->whereIn($pk, $data['ids'])->where('tag_id', '>', 0)->find();
        if ($info) {
            // return '内容存在标签，请在[标签]或[修改]解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
