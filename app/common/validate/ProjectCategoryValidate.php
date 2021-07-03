<?php
/*
 * @Description  : 案例分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-29
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\ProjectService;
use app\common\service\ProjectCategoryService;

class ProjectCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'project_category'    => ['require', 'array'],
        'project_category_id' => ['require'],
        'category_name'       => ['require', 'checkCategoryName'],
        'image'               => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'             => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'project_category_id'   => '缺少参数：案例分类id',
        'category_name.require' => '请输入分类名称',
        'image.require'         => '请选择图片',
        'image.file'            => '请选择上传图片',
        'image.image'           => '请选择图片格式文件',
        'image.fileExt'         => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'        => '请选择小于500kb的图片',
        'is_hide.require'       => 'is_hide must',
        'is_hide.in'            => 'is_hide 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['project_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['project_category_id', 'category_name'],
        'dele'   => ['project_category'],
        'ishide' => ['project_category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类名称删除
    protected function scenedele()
    {
        return $this->only(['project_category'])
            ->append('project_category', ['checkProjectCategory', 'checkProject']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = ProjectCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在案例
    protected function checkProject($value, $rule, $data = [])
    {
        $project_category_ids = array_column($value, 'project_category_id');
        
        $where[] = ['project_category_id', 'in', $project_category_ids];
        $where[] = ['is_delete', '=', 0];

        $project = ProjectService::list($where);
        if ($project['list']) {
            return '分类下存在案例，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkProjectCategory($value, $rule, $data = [])
    {
        $project_category_ids = array_column($value, 'project_category_id');

        $project_category = ProjectCategoryService::list('list');
        foreach ($project_category as $k => $v) {
            foreach ($project_category_ids as $ka => $va) {
                if ($v['project_category_pid'] == $va) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }
}
