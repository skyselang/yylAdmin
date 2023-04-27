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
use app\common\model\content\TagModel;
use app\common\model\content\AttributesModel;

/**
 * 内容标签验证器
 */
class TagValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'tag_id'      => ['require'],
        'tag_name'    => ['require', 'checkExisted'],
        'content_ids' => ['array'],
    ];

    // 错误信息
    protected $message = [
        'tag_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'          => ['tag_id'],
        'add'           => ['tag_name'],
        'edit'          => ['tag_id', 'tag_name'],
        'dele'          => ['ids'],
        'disable'       => ['ids'],
        'content'       => ['tag_id'],
        'contentRemove' => ['tag_id', 'content_ids'],
    ];

    // 验证场景定义：标签删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkContent');
    }

    // 自定义验证规则：标签是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new TagModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['tag_name', '=', $data['tag_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '名称已存在：' . $data['tag_name'];
        }

        $unique = $data['tag_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return '标识不能为纯数字';
            }
            $where_unique[] = [$pk, '<>', $id];
            $where_unique[] = ['tag_unique', '=', $unique];
            $where_unique = where_delete($where_unique);
            $info = $model->field($pk)->where($where_unique)->find();
            if ($info) {
                return '标识已存在：' . $unique;
            }
        }

        return true;
    }

    // 自定义验证规则：标签下是否存在内容
    protected function checkContent($value, $rule, $data = [])
    {
        // $info = AttributesModel::where('tag_id', 'in', $data['ids'])->find();
        // if ($info) {
        //     return '标签下存在内容，请在[内容]中解除后再删除：' . $info['tag_id'];
        // }

        return true;
    }
}
