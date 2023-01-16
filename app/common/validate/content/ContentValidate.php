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

/**
 * 内容管理验证器
 */
class ContentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'content_id' => ['require'],
        'name'       => ['require', 'checkExisted'],
        'images'     => ['array'],
        'videos'     => ['array'],
        'annexs'     => ['array'],
        'is_top'     => ['require', 'in' => '0,1'],
        'is_hot'     => ['require', 'in' => '0,1'],
        'is_rec'     => ['require', 'in' => '0,1'],
        'is_disable' => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'name.require'  => '请输入名称',
        'is_top.in'     => '是否置顶，1是0否',
        'is_hot.in'     => '是否热门，1是0否',
        'is_rec.in'     => '是否推荐，1是0否',
        'is_disable.in' => '是否禁用，1是0否',
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
        'disable'  => ['ids', 'is_disable']
    ];

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
}
