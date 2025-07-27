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
use app\common\service\member\TagService as Service;
use app\common\model\member\TagModel as Model;
use app\common\model\member\AttributesModel;

/**
 * 会员标签验证器
 */
class TagValidate extends Validate
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
        'tag_id'      => ['require'],
        'tag_name'    => ['require', 'checkExisted'],
        'import_file' => ['require', 'file', 'fileExt' => 'xlsx'],
        'member_ids'  => ['array'],
    ];

    // 错误信息
    protected $message = [
        'tag_name.require'    => '请输入名称',
        'import_file.require' => '请选择导入文件',
        'import_file.fileExt' => '只允许xlsx文件格式',
    ];

    // 验证场景
    protected $scene = [
        'info'       => ['tag_id'],
        'add'        => ['tag_name'],
        'edit'       => ['tag_id', 'tag_name'],
        'dele'       => ['ids'],
        'disable'    => ['ids'],
        'update'     => ['ids', 'field'],
        'import'     => ['import_file'],
        'memberList' => ['tag_id'],
        'memberLift' => ['tag_id', 'member_ids'],
    ];

    // 验证场景定义：标签删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkMember');
    }

    // 自定义验证规则：标签是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['tag_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['tag_unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], ['tag_name', '=', $data['tag_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['tag_name'];
        }

        return true;
    }

    // 自定义验证规则：标签批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    // 自定义验证规则：标签是否存在会员
    protected function checkMember($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $info  = AttributesModel::field($pk)->where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '标签存在会员，请在[会员]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
