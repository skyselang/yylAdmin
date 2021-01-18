<?php
/*
 * @Description  : 接口环境验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-01-14
 * @LastEditTime : 2021-01-15
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\ApiEnvService;

class ApiEnvValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'api_env_id' => ['require', 'checkApiEnv'],
        'name'       => ['require', 'checkApiEnvName'],
        'host'       => ['require'],
    ];

    // 错误信息
    protected $message = [
        'api_env_id.require' => '缺少参数：接口环境id',
        'name.require'       => '请输入接口环境名称',
        'host.require'       => '请输入接口环境Host',
    ];

    // 验证场景
    protected $scene = [
        'id'   => ['api_env_id'],
        'add'  => ['name', 'host'],
        'edit' => ['api_env_id', 'name', 'host'],
        'dele' => ['api_env_id'],
    ];

    // 自定义验证规则：接口环境是否存在
    protected function checkApiEnv($value, $rule, $data = [])
    {
        $api_env_id = $value;

        $api_env = ApiEnvService::info($api_env_id);

        if ($api_env['is_delete'] == 1) {
            return '接口环境已删除：' . $api_env_id;
        }

        return true;
    }

    // 自定义验证规则：接口环境是否已存在
    protected function checkApiEnvName($value, $rule, $data = [])
    {
        $api_env_id = isset($data['api_env_id']) ? $data['api_env_id'] : '';

        if ($api_env_id) {
            $where[] = ['api_env_id', '<>', $api_env_id];
        }
        $where[] = ['name', '=', $data['name']];
        $where[] = ['is_delete', '=', 0];

        $api_env = Db::name('api_env')
            ->field('api_env_id')
            ->where($where)
            ->find();

        if ($api_env) {
            return '接口环境已存在：' . $data['name'];
        }

        return true;
    }
}
