<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\setting;

use think\Validate;
use app\common\model\setting\RegionModel;

/**
 * 地区管理验证器
 */
class RegionValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'region_id'   => ['require'],
        'region_pid'  => ['checkRegionPid'],
        'region_name' => ['require', 'checkRegionExist'],
    ];

    // 错误信息
    protected $message = [
        'region_name.require' => '请输入地区名称',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['region_id'],
        'add'      => ['region_name'],
        'edit'     => ['region_id', 'region_pid', 'region_name'],
        'dele'     => ['ids'],
        'editpid'  => ['ids', 'region_pid'],
        'citycode' => ['ids'],
        'zipcode'  => ['ids'],
        'disable'  => ['ids'],
    ];

    // 验证场景定义：地区删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkRegionChild');
    }

    // 自定义验证规则：地区上级
    protected function checkRegionPid($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        if ($data['region_id'] ?? 0) {
            $ids[] = $data['region_id'];
        }

        foreach ($ids as $id) {
            if ($data['region_pid'] == $id) {
                return '地区上级不能等于地区本身';
            }
        }

        return true;
    }

    // 自定义验证规则：地区是否已存在
    protected function checkRegionExist($value, $rule, $data = [])
    {
        $model = new RegionModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;
        $pid = $data['region_pid'] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['region_pid', '=', $pid];
        $where[] = ['region_name', '=', $data['region_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '地区名称已存在：' . $data['region_name'];
        }

        return true;
    }

    // 自定义验证规则：地区是否存在下级地区
    protected function checkRegionChild($value, $rule, $data = [])
    {
        $info = RegionModel::field('region_pid')->where(where_delete(['region_pid', 'in', $data['ids']]))->find();
        if ($info) {
            return '地区存在下级地区，无法删除：' . $info['region_pid'];
        }

        return true;
    }
}
