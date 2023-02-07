<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\file;

use think\Validate;
use app\common\model\file\TagModel;
use app\common\model\file\TagsModel;

/**
 * 文件标签验证器
 */
class TagValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'      => ['require', 'array'],
        'tag_id'   => ['require'],
        'tag_name' => ['require', 'checkExisted'],
        'file_ids' => ['array'],
    ];

    // 错误信息
    protected $message = [
        'tag_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['tag_id'],
        'add'         => ['tag_name'],
        'edit'        => ['tag_id', 'tag_name'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'file'        => ['tag_id'],
        'fileRemove'  => ['tag_id', 'file_ids'],
    ];

    // 验证场景定义：标签删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkFile');
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

        return true;
    }

    // 自定义验证规则：标签下是否存在文件
    protected function checkFile($value, $rule, $data = [])
    {
        // $info = TagsModel::where('tag_id', 'in', $data['ids'])->find();
        // if ($info) {
        //     return '标签下存在文件，请在[文件]中解除后再删除：' . $info['tag_id'];
        // }

        return true;
    }
}
