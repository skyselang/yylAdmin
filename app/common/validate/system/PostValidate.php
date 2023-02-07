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
use app\common\model\system\PostModel;
use app\common\model\system\UserAttributesModel;

/**
 * 职位管理验证器
 */
class PostValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'post_id'   => ['require'],
        'post_pid'  => ['checkPid'],
        'post_name' => ['require', 'checkExisted'],
        'user_ids'  => ['array'],
    ];

    // 错误信息
    protected $message = [
        'post_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'       => ['post_id'],
        'add'        => ['post_name'],
        'edit'       => ['post_id', 'post_pid', 'post_name'],
        'dele'       => ['ids'],
        'editpid'    => ['ids', 'post_pid'],
        'disable'    => ['ids'],
        'user'       => ['post_id'],
        'userRemove' => ['post_id', 'user_ids'],
    ];

    // 验证场景定义：职位删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkUser');
    }

    // 自定义验证规则：职位上级
    protected function checkPid($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        if ($data['post_id'] ?? 0) {
            $ids[] = $data['post_id'];
        }

        foreach ($ids as $id) {
            if ($data['post_pid'] == $id) {
                return '职位上级不能等于职位本身';
            }
        }

        return true;
    }

    // 自定义验证规则：职位是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new PostModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['post_name', '=', $data['post_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '名称已存在：' . $data['post_name'];
        }

        return true;
    }

    // 自定义验证规则：职位下是否存在用户
    protected function checkUser($value, $rule, $data = [])
    {
        // $info = UserAttributesModel::where('post_id', 'in', $data['ids'])->find();
        // if ($info) {
        //     return '职位下存在用户，请在[用户]中解除后再删除：' . $info['post_id'];
        // }

        return true;
    }
}
