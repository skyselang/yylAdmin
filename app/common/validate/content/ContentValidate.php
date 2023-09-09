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
use app\common\model\content\ContentModel;
use app\common\model\content\AttributesModel;

/**
 * 内容管理验证器
 */
class ContentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'          => ['require', 'array'],
        'content_id'   => ['require'],
        'name'         => ['require', 'checkExisted'],
        'release_time' => ['date'],
        'is_top'       => ['require', 'in' => '0,1'],
        'is_hot'       => ['require', 'in' => '0,1'],
        'is_rec'       => ['require', 'in' => '0,1'],
        'is_disable'   => ['require', 'in' => '0,1'],
        'images'       => ['array'],
        'videos'       => ['array'],
        'annexs'       => ['array'],
        
    ];

    // 错误信息
    protected $message = [
        'name.require'      => '请输入名称',
        'release_time.date' => '发布时间不是一个有效的日期或时间格式',
        'is_top.in'         => '是否置顶，1是0否',
        'is_hot.in'         => '是否热门，1是0否',
        'is_rec.in'         => '是否推荐，1是0否',
        'is_disable.in'     => '是否禁用，1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['content_id'],
        'add'      => ['name', 'images', 'videos', 'annexs'],
        'edit'     => ['content_id', 'name', 'images', 'videos', 'annexs'],
        'dele'     => ['ids'],
        'editcate' => ['ids'],
        'edittag'  => ['ids'],
        'istop'    => ['ids', 'is_top'],
        'ishot'    => ['ids', 'is_hot'],
        'isrec'    => ['ids', 'is_rec'],
        'disable'  => ['ids', 'is_disable'],
        'release'  => ['ids', 'release_time'],
    ];

    // 验证场景定义：后台删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkCategoryTag');
    }

    // 自定义验证规则：内容是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $unique = $data['unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return '标识不能为纯数字';
            }

            $model = new ContentModel();
            $pk = $model->getPk();
            $id = $data[$pk] ?? 0;

            $where[] = [$pk, '<>', $id];
            $where[] = ['unique', '=', $unique];
            $where = where_delete($where);
            $info = $model->field($pk)->where($where)->find();
            if ($info) {
                return '标识已存在：' . $unique;
            }
        }

        return true;
    }

    // 自定义验证规则：内容是否存在分类或标签
    protected function checkCategoryTag($value, $rule, $data = [])
    {
        // $info = AttributesModel::field('content_id')->where('content_id', 'in', $data['ids'])->where('category_id', '>', 0)->find();
        // if ($info) {
        //     return '内容存在分类，请在[分类]或[修改]解除后再删除：' . $info['content_id'];
        // }

        // $info = AttributesModel::field('content_id')->where('content_id', 'in', $data['ids'])->where('tag_id', '>', 0)->find();
        // if ($info) {
        //     return '内容存在标签，请在[标签]或[修改]解除后再删除：' . $info['content_id'];
        // }

        return true;
    }
}
