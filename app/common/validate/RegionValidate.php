<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 地区管理验证器
namespace app\common\validate;

use think\Validate;
use app\common\service\RegionService;

class RegionValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'region_id'   => ['require'],
        'region_name' => ['require', 'checkRegionName'],
    ];

    // 错误信息
    protected $message = [
        'region_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'id'   => ['region_id'],
        'info' => ['region_id'],
        'add'  => ['region_name'],
        'edit' => ['region_id', 'region_name'],
        'dele' => ['ids'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkRegionChild');
    }

    // 自定义验证规则：地区名称是否已存在
    protected function checkRegionName($value, $rule, $data = [])
    {
        if (isset($data['region_id'])) {
            if ($data['region_pid'] == $data['region_id']) {
                return '地区父级不能等于地区自己';
            }
            $where[] = ['region_id', '<>', $data['region_id']];
        }
        $where[] = ['region_pid', '=', $data['region_pid']];
        $where[] = ['region_name', '=', $data['region_name']];
        $region = RegionService::list($where, [], 'region_id');
        if ($region['list']) {
            return '地区名称已存在：' . $data['region_name'];
        }

        return true;
    }

    // 自定义验证规则：地区是否存在下级地区
    protected function checkRegionChild($value, $rule, $data = [])
    {
        $where[] = ['region_pid', 'in', $data['ids']];
        $region = RegionService::list($where, [], 'region_id');
        if ($region['list']) {
            return '存在下级地区，无法删除';
        }

        return true;
    }
}
