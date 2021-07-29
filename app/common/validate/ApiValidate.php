<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口管理验证器
namespace app\common\validate;

use think\Validate;
use app\common\service\ApiService;

class ApiValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'api_id'   => ['require'],
        'api_name' => ['require', 'checkApiExist'],
    ];

    // 错误信息
    protected $message = [
        'api_id.require'   => '缺少参数：接口id',
        'api_name.require' => '请输入接口名称',
    ];

    // 验证场景
    protected $scene = [
        'id'      => ['api_id'],
        'info'    => ['api_id'],
        'add'     => ['api_name'],
        'edit'    => ['api_id', 'api_name'],
        'dele'    => ['api_id'],
        'disable' => ['api_id'],
        'unlogin' => ['api_id'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['api_id'])
            ->append('api_id', 'checkApiChild');
    }

    // 自定义验证规则：接口是否已存在
    protected function checkApiExist($value, $rule, $data = [])
    {
        $api_id = isset($data['api_id']) ? $data['api_id'] : '';
        if ($api_id) {
            if ($data['api_pid'] == $data['api_id']) {
                return '接口父级不能等于接口自己';
            }
        }

        $where_name[] = ['api_id', '<>', $api_id];
        $where_name[] = ['api_pid', '=', $data['api_pid']];
        $where_name[] = ['api_name', '=', $data['api_name']];
        $where_name[] = ['is_delete', '=', 0];
        $api_name = ApiService::list($where_name, 1, 1, [], 'api_id');
        if ($api_name['list']) {
            return '接口名称已存在：' . $data['api_name'];
        }

        if ($data['api_url']) {
            $where_url[] = ['api_id', '<>', $api_id];
            $where_url[] = ['api_url', '=', $data['api_url']];
            $where_url[] = ['is_delete', '=', 0];
            $api_url = ApiService::list($where_url, 1, 1, [], 'api_id');
            if ($api_url['list']) {
                return '接口链接已存在：' . $data['api_url'];
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否有子级接口
    protected function checkApiChild($value, $rule, $data = [])
    {
        $where_pid[] = ['api_pid', '=', $data['api_id']];
        $where_pid[] = ['is_delete', '=', 0];
        $api_pid = ApiService::list($where_pid, 1, 1, [], 'api_id');
        if ($api_pid['list']) {
            return '请删除所有子级接口后再删除';
        }

        return true;
    }
}
