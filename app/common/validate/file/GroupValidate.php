<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件分组验证器
namespace app\common\validate\file;

use think\Validate;
use app\common\service\file\FileService;
use app\common\service\file\GroupService;

class GroupValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'group'      => ['require', 'array'],
        'group_id'   => ['require'],
        'group_name' => ['require', 'checkGroupName'],
    ];

    // 错误信息
    protected $message = [
        'group_name.require' => '请输入分组名称',
    ];

    // 验证场景
    protected $scene = [
        'id'      => ['group_id'],
        'info'    => ['group_id'],
        'add'     => ['group_name'],
        'edit'    => ['group_id', 'group_name'],
        'dele'    => ['group'],
        'disable' => ['group'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['group'])
            ->append('group', 'checkGroupFile');
    }

    // 自定义验证规则：分组名称是否已存在
    protected function checkGroupName($value, $rule, $data = [])
    {
        if (isset($data['group_id'])) {
            $where[] = ['group_id', '<>', $data['group_id']];
        }
        $where[] = ['group_name', '=', $data['group_name']];
        $where[] = ['is_delete', '=', 0];
        $group = GroupService::list($where, 1, 1, [], 'group_id');
        if ($group['list']) {
            return '分组名称已存在：' . $data['group_name'];
        }

        return true;
    }

    // 自定义验证规则：分组是否有文件
    protected function checkGroupFile($value, $rule, $data = [])
    {
        $group_ids = array_column($data['group'], 'group_id');
        $where[] = ['group_id', 'in', $group_ids];
        $file = FileService::list($where, 1, 1, [], 'file_id');
        if ($file['list']) {
            return '分组下有文件，无法删除';
        }

        return true;
    }
}
