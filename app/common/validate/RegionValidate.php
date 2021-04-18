<?php
/*
 * @Description  : 地区管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-08
 * @LastEditTime : 2021-04-13
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;
use app\common\service\RegionService;

class RegionValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'region_id'   => ['require', 'checkRegion'],
        'region_name' => ['require', 'checkRegionName'],
    ];

    // 错误信息
    protected $message = [
        'region_id.require'   => 'region_id must',
        'region_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'id'   => ['region_id'],
        'info' => ['region_id'],
        'add'  => ['region_name'],
        'edit' => ['region_id', 'region_name'],
        'dele' => ['region_id'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['region_id'])
            ->append('region_id', 'checkRegionChild');
    }

    // 自定义验证规则：地区是否存在
    protected function checkRegion($value, $rule, $data = [])
    {
        $region_id = $value;

        $region = RegionService::info($region_id);

        if ($region['is_delete'] == 1) {
            return '地区已被删除：' . $region_id;
        }

        return true;
    }

    // 自定义验证规则：地区名称是否已存在
    protected function checkRegionName($value, $rule, $data = [])
    {
        $region_id = isset($data['region_id']) ? $data['region_id'] : '';

        if ($region_id) {
            if ($data['region_pid'] == $data['region_id']) {
                return '地区父级不能等于地区本身';
            }
        }

        $region = Db::name('region')
            ->field('region_id')
            ->where('region_id', '<>', $region_id)
            ->where('region_pid', '=', $data['region_pid'])
            ->where('region_name', '=', $data['region_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($region) {
            return '地区名称已存在：' . $data['region_name'];
        }

        return true;
    }

    // 自定义验证规则：地区是否有子级地区
    protected function checkRegionChild($value, $rule, $data = [])
    {
        $region_id = $value;

        $region = Db::name('region')
            ->field('region_id')
            ->where('region_pid', '=', $region_id)
            ->where('is_delete', '=', 0)
            ->find();

        if ($region) {
            return '请删除所有子级地区后再删除';
        }

        return true;
    }
}
