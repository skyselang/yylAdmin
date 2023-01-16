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
use app\common\model\member\ApiModel;
use app\common\model\member\GroupApisModel;

/**
 * 会员接口验证器
 */
class ApiValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'api_id'    => ['require'],
        'api_pid'   => ['checkPid'],
        'api_name'  => ['require', 'checkExisted'],
        'group_ids' => ['array'],
    ];

    // 错误信息
    protected $message = [
        'api_name.require' => '请输入接口名称',
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['api_id'],
        'add'         => ['api_name'],
        'edit'        => ['api_id', 'api_pid', 'api_name'],
        'dele'        => ['ids'],
        'editsort'    => ['ids'],
        'editpid'     => ['ids', 'api_pid'],
        'unlogin'     => ['ids'],
        'unauth'      => ['ids'],
        'unrate'      => ['ids'],
        'disable'     => ['ids'],
        'group'       => ['api_id'],
        'groupRemove' => ['api_id', 'group_ids'],
    ];

    // 验证场景定义：接口删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkGroup']);
    }

    // 自定义验证规则：接口上级
    protected function checkPid($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        if ($data['api_id'] ?? 0) {
            $ids[] = $data['api_id'];
        }

        foreach ($ids as $id) {
            if ($data['api_pid'] == $id) {
                return '接口上级不能等于接口本身';
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new ApiModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;
        $pid = $data['api_pid'] ?? 0;

        $where_name[] = [$pk, '<>', $id];
        $where_name[] = ['api_pid', '=', $pid];
        $where_name[] = ['api_name', '=', $data['api_name']];
        $where_name = where_delete($where_name);
        $info = $model->field($pk)->where($where_name)->find();
        if ($info) {
            return '接口名称已存在：' . $data['api_name'];
        }

        $url = $data['api_url'] ?? '';
        if ($url) {
            $where_url[] = [$pk, '<>', $id];
            $where_url[] = ['api_url', '=', $url];
            $where_url = where_delete($where_url);
            $info = $model->field($pk)->where($where_url)->find();
            if ($info) {
                return '接口链接已存在：' . $url;
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否存在下级接口
    protected function checkChild($value, $rule, $data = [])
    {
        $where = where_delete(['api_pid', 'in', $data['ids']]);
        $info = ApiModel::field('api_pid')->where($where)->find();
        if ($info) {
            return '接口存在下级接口，无法删除：' . $info['api_pid'];
        }

        return true;
    }

    // 自定义验证规则：接口是否存在分组
    protected function checkGroup($value, $rule, $data = [])
    {
        $info = GroupApisModel::field('api_id')->where('api_id', 'in', $data['ids'])->find();
        if ($info) {
            return '接口存在分组，请在[分组]中解除后再删除：' . $info['api_id'];
        }

        return true;
    }
}
