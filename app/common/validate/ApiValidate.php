<?php
/*
 * @Description  : 接口管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-07
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;
use app\common\service\ApiService;

class ApiValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'api_id'   => ['require', 'checkApiId'],
        'api_name' => ['require', 'checkApi'],
    ];

    // 错误信息
    protected $message = [
        'api_id.require'   => '缺少参数：接口id',
        'api_name.require' => '请输入接口名称',
    ];

    // 验证场景
    protected $scene = [
        'id'     => ['api_id'],
        'info'   => ['api_id'],
        'add'    => ['api_name'],
        'edit'   => ['api_id', 'api_name'],
        'dele'   => ['api_id'],
        'unauth' => ['api_id'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['api_id'])
            ->append('api_id', 'checkApiChild');
    }

    // 自定义验证规则：接口是否存在
    protected function checkApiId($value, $rule, $data = [])
    {
        $api_id = $value;

        $api = ApiService::info($api_id);

        if ($api['is_delete'] == 1) {
            return '接口已删除：' . $api_id;
        }

        return true;
    }

    // 自定义验证规则：接口是否已存在
    protected function checkApi($value, $rule, $data = [])
    {
        $api_id = isset($data['api_id']) ? $data['api_id'] : '';

        if ($api_id) {
            if ($data['api_pid'] == $data['api_id']) {
                return '接口父级不能等于接口本身';
            }
        }

        $api_name = Db::name('api')
            ->field('api_id')
            ->where('api_id', '<>', $api_id)
            ->where('api_pid', '=', $data['api_pid'])
            ->where('api_name', '=', $data['api_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($api_name) {
            return '接口名称已存在：' . $data['api_name'];
        }

        if ($data['api_url']) {
            $api_url = Db::name('api')
                ->field('api_id')
                ->where('api_id', '<>', $api_id)
                ->where('api_url', '=', $data['api_url'])
                ->where('api_url', '<>', '')
                ->where('is_delete', '=', 0)
                ->find();

            if ($api_url) {
                return '接口链接已存在：' . $data['api_url'];
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否有子级接口
    protected function checkApiChild($value, $rule, $data = [])
    {
        $api_id = $value;

        $api = Db::name('api')
            ->field('api_id')
            ->where('api_pid', '=', $api_id)
            ->where('is_delete', '=', 0)
            ->find();

        if ($api) {
            return '请删除所有子级接口后再删除';
        }

        return true;
    }
}
