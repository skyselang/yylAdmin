<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\content;

use think\Validate;
use app\common\model\content\CategoryModel;
use app\common\model\content\AttributesModel;

/**
 * 内容分类验证器
 */
class CategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'           => ['require', 'array'],
        'category_id'   => ['require'],
        'category_pid'  => ['checkPid'],
        'category_name' => ['require', 'checkExisted'],
        'is_disable'    => ['require', 'in' => '0,1'],
        'images'        => ['array'],
        'content_ids'   => ['array'],
    ];

    // 错误信息
    protected $message = [
        'category_name.require' => '请输入名称',
        'is_disable.in'         => '是否禁用，1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'          => ['category_id'],
        'add'           => ['category_name', 'images'],
        'edit'          => ['category_id', 'category_pid', 'category_name', 'images'],
        'dele'          => ['ids'],
        'editpid'       => ['ids', 'category_pid'],
        'disable'       => ['ids', 'is_disable'],
        'content'       => ['category_id'],
        'contentRemove' => ['category_id', 'content_ids'],
    ];

    // 验证场景定义：分类删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkChild', 'checkContent']);
    }

    // 自定义验证规则：分类上级
    protected function checkPid($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        if ($data['category_id'] ?? 0) {
            $ids[] = $data['category_id'];
        }

        foreach ($ids as $id) {
            if ($data['category_pid'] == $id) {
                return '分类上级不能等于分类本身';
            }
        }

        return true;
    }

    // 自定义验证规则：分类是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new CategoryModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;
        $pid = $data['category_pid'] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['category_pid', '=', $pid];
        $where[] = ['category_name', '=', $data['category_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '名称已存在：' . $data['category_name'];
        }

        $unique = $data['category_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return '标识不能为纯数字';
            }
            $where_unique[] = [$pk, '<>', $id];
            $where_unique[] = ['category_unique', '=', $unique];
            $where_unique = where_delete($where_unique);
            $info = $model->field($pk)->where($where_unique)->find();
            if ($info) {
                return '标识已存在：' . $unique;
            }
        }

        return true;
    }

    // 自定义验证规则：分类是否存在下级分类
    protected function checkChild($value, $rule, $data = [])
    {
        $where = where_delete(['category_pid', 'in', $data['ids']]);
        $info = CategoryModel::field('category_pid')->where($where)->find();
        if ($info) {
            return '分类存在下级分类，无法删除：' . $info['category_pid'];
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在内容
    protected function checkContent($value, $rule, $data = [])
    {
        $info = AttributesModel::where('category_id', 'in', $data['ids'])->find();
        if ($info) {
            return '分类下存在内容，请在[内容]中解除后再删除：' . $info['category_id'];
        }

        return true;
    }
}
