<?php
/*
 * @Description  : 地区验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-08
 * @LastEditTime : 2020-12-08
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class RegionValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'region_id'        => ['require'],
        'region_name'      => ['require', 'checkRegion'],
        'region_pinyin'    => ['require'],
        'region_jianpin'   => ['require'],
        'region_initials'  => ['require'],
        'region_citycode'  => ['require'],
        'region_zipcode'   => ['require'],
        'region_longitude' => ['require'],
        'region_latitude'  => ['require'],
    ];

    // 错误信息
    protected $message  =   [
        'region_id.require'        => '缺少参数：region_id',
        'region_name.require'      => '请输入名称',
        'region_pinyin.require'    => '请输入拼音',
        'region_jianpin.require'   => '请输入简拼',
        'region_initials.require'  => '请输入首字母',
        'region_citycode.require'  => '请输入区号',
        'region_zipcode.require'   => '请输入邮编',
        'region_longitude.require' => '请输入经度',
        'region_latitude.require'  => '请输入纬度',
    ];

    // 验证场景
    protected $scene = [
        'region_id'   => ['region_id'],
        'region_add'  => ['region_name', 'region_citycode', 'region_zipcode', 'region_longitude', 'region_latitude'],
        'region_edit' => ['region_id', 'region_name', 'region_citycode', 'region_zipcode', 'region_longitude', 'region_latitude'],
        'region_dele' => ['region_id'],
    ];

    // 验证场景定义：删除
    protected function sceneregion_dele()
    {
        return $this->only(['region_id'])
            ->append('region_id', 'checkRegionChild');
    }

    // 自定义验证规则：地区是否存在
    protected function checkRegion($value, $rule, $data = [])
    {
        $region_id = isset($data['region_id']) ? $data['region_id'] : '';

        if ($region_id) {
            if ($data['region_pid'] == $data['region_id']) {
                return '地区父级不能等于地区本身';
            }
        }

        $region_name = Db::name('region')
            ->field('region_id')
            ->where('region_id', '<>', $region_id)
            ->where('region_pid', '=', $data['region_pid'])
            ->where('region_name', '=', $data['region_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($region_name) {
            return '地区已存在：' . $data['region_name'];
        }

        return true;
    }

    // 自定义验证规则：地区是否有子地区
    protected function checkRegionChild($value, $rule, $data = [])
    {
        $region_id = $value;

        $region = Db::name('region')
            ->field('region_id')
            ->where('region_pid', '=', $region_id)
            ->where('is_delete', '=', 0)
            ->find();

        if ($region) {
            return '请删除所有子地区后再删除';
        }

        return true;
    }
}
