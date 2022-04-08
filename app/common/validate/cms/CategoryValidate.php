<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容分类验证器
namespace app\common\validate\cms;

use think\Validate;
use app\common\model\cms\CategoryModel;
use app\common\model\cms\ContentModel;

class CategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'           => ['require', 'array'],
        'category_id'   => ['require'],
        'category_name' => ['require', 'checkCategoryName'],
        'is_hide'       => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'category_name.require' => '请输入分类名称',
        'is_hide.in'            => '是否隐藏，1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['category_id'],
        'add'    => ['category_name'],
        'edit'   => ['category_id', 'category_name'],
        'dele'   => ['ids'],
        'pid'    => ['ids'],
        'ishide' => ['ids', 'is_hide'],
        'reco'   => ['ids'],
    ];

    // 验证场景定义：分类删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkCategoryChildren', 'checkCategoryContent']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $CategoryModel = new CategoryModel();
        $CategoryPk = $CategoryModel->getPk();

        $category_id  = isset($data[$CategoryPk]) ? $data[$CategoryPk] : '';
        $category_pid = isset($data['category_pid']) ? $data['category_pid'] : 0;

        if ($category_id) {
            if ($category_id == $category_pid) {
                return '分类上级不能等于分类本身';
            }
            $where[] = [$CategoryPk, '<>', $category_id];
        }
        $where[] = ['category_pid', '=', $category_pid];
        $where[] = ['category_name', '=', $data['category_name']];
        $where[] = ['is_delete', '=', 0];

        $category = $CategoryModel->field($CategoryPk)->where($where)->find();
        if ($category) {
            return '分类名称已存在：' . $data['category_name'];
        }

        return true;
    }

    // 自定义验证规则：分类是否存在下级分类
    protected function checkCategoryChildren($value, $rule, $data = [])
    {
        $CategoryModel = new CategoryModel();
        $CategoryPk = $CategoryModel->getPk();

        $recycle = isset($data['recycle']) ? $data['recycle'] : 0;
        $where[] = ['category_pid', 'in', $data['ids']];
        if ($recycle == 1) {
            $where[] = ['is_delete', '=', 1];
        } else {
            $where[] = ['is_delete', '=', 0];
        }
        $category = $CategoryModel->field($CategoryPk)->where($where)->find();
        if ($category) {
            return '分类存在下级分类，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类是否存在内容
    protected function checkCategoryContent($value, $rule, $data = [])
    {
        $CategoryModel = new CategoryModel();
        $CategoryPk = $CategoryModel->getPk();

        $ContentModel = new ContentModel();
        $ContentPk = $ContentModel->getPk();

        $where[] = [$CategoryPk, 'in', $data['ids']];
        $where[] = ['is_delete', '=', 0];
        $content = $ContentModel->field($ContentPk)->where($where)->find();
        if ($content) {
            return '分类下存在内容，无法删除';
        }

        return true;
    }
}
