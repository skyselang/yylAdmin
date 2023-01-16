<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\file;

use think\Validate;
use app\common\model\file\GroupModel;
use app\common\model\file\FileModel;

/**
 * 文件分组验证器
 */
class GroupValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'group_id'   => ['require'],
        'group_name' => ['require', 'checkExisted'],
        'file_ids'   => ['array'],
    ];

    // 错误信息
    protected $message = [
        'group_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'       => ['group_id'],
        'add'        => ['group_name'],
        'edit'       => ['group_id', 'group_name'],
        'dele'       => ['ids'],
        'disable'    => ['ids'],
        'file'       => ['group_id'],
        'fileRemove' => ['group_id', 'file_ids'],
    ];

    // 验证场景定义：删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkGroupFile');
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

    // 自定义验证规则：分组下是否存在文件
    protected function checkGroupFile($value, $rule, $data = [])
    {
        $where = where_delete(['group_id', 'in', $data['ids']]);
        $info = FileModel::field('group_id')->where($where)->find();
        if ($info) {
            return '分组下存在文件，请在[文件]中解除后再删除：' . $info['group_id'];
        }

        return true;
    }
}
