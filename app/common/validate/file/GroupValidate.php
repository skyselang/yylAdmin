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
        'group_name' => ['require', 'checkGroupName'],
    ];

    // 错误信息
    protected $message = [
        'group_name.require' => '请输入分组名称',
    ];

    // 验证场景
    protected $scene = [
        'id'          => ['group_id'],
        'info'        => ['group_id'],
        'add'         => ['group_name'],
        'edit'        => ['group_id', 'group_name'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'recoverReco' => ['ids'],
        'recoverDele' => ['ids'],
    ];

    // 验证场景定义：删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkGroupFile');
    }

    // 验证场景定义：恢复删除
    protected function sceneRecoverReco()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkRecoverReco');
    }

    // 自定义验证规则：分组名称是否已存在
    protected function checkGroupName($value, $rule, $data = [])
    {
        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        if (isset($data[$GroupPk])) {
            $where[] = [$GroupPk, '<>', $data[$GroupPk]];
        }
        $where[] = ['group_name', '=', $data['group_name']];
        $where[] = ['is_delete', '=', 0];
        $group = $GroupModel->field($GroupPk)->where($where)->find();
        if ($group) {
            return '分组名称已存在：' . $data['group_name'];
        }

        return true;
    }

    // 自定义验证规则：分组下是否有文件
    protected function checkGroupFile($value, $rule, $data = [])
    {
        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        $FileModel = new FileModel();
        $FilePk = $FileModel->getPk();

        $where[] = [$GroupPk, 'in', $data['ids']];
        $where[] = ['is_delete', '=', 0];
        $file = $FileModel->field($FilePk)->where($where)->find();
        if ($file) {
            return '分组下有文件，无法删除';
        }

        return true;
    }

    // 自定义验证规则：回收站恢复：分组名称是否已存在
    protected function checkRecoverReco($value, $rule, $data = [])
    {
        $GroupModel = new GroupModel();
        $GroupPk = $GroupModel->getPk();

        $member = $GroupModel->field('group_name')->where($GroupPk, 'in', $data['ids'])->select()->toArray();
        $group_names = array_filter(array_column($member, 'group_name'));
        $group_name = $GroupModel->field('group_name')->where('group_name', 'in', $group_names)->where('is_delete', 0)->find();
        if ($group_name) {
            return '分组名称已存在：' . $group_name['group_name'];
        }

        return true;
    }
}
