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
use app\common\service\member\GroupService as Service;
use app\common\model\member\GroupModel as Model;
use app\common\model\member\AttributesModel;
use app\common\model\member\GroupApisModel;

/**
 * 会员分组验证器
 */
class GroupValidate extends Validate
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
        'group_id'    => ['require'],
        'group_name'  => ['require', 'checkExisted'],
        'api_ids'     => ['array'],
        'import_file' => ['require', 'file', 'fileExt' => 'xlsx'],
        'member_ids'  => ['array'],
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'group_name.require'  => '请输入名称',
        'import_file.require' => '请选择导入文件',
        'import_file.fileExt' => '只允许xlsx文件格式',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'info'       => ['group_id'],
        'add'        => ['group_name'],
        'edit'       => ['group_id', 'group_name'],
        'dele'       => ['ids'],
        'disable'    => ['ids'],
        'update'     => ['ids', 'field'],
        'import'     => ['import_file'],
        'memberList' => ['group_id'],
        'memberLift' => ['group_id', 'member_ids'],
    ];

    /**
     * 验证场景定义：分组删除
     */
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkApiMember');
    }

    /**
     * 自定义验证规则：分组是否已存在
     */
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['group_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['group_unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], ['group_name', '=', $data['group_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['group_name'];
        }

        return true;
    }

    /**
     * 自定义验证规则：分组批量修改字段
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
     * 自定义验证规则：分组是否存在接口或会员
     */
    protected function checkApiMember($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();

        $info = GroupApisModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '分组存在接口，请在[接口]或[修改]中解除后再删除：' . $info[$pk];
        }

        $info = AttributesModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '分组存在会员，请在[会员]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
