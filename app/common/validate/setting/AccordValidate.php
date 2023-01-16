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
use app\common\model\setting\AccordModel;

/**
 * 协议管理验证器
 */
class AccordValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'accord_id' => ['require'],
        'unique'    => ['require', 'checkExisted'],
        'name'      => ['require'],
    ];

    // 错误信息
    protected $message = [
        'unique.require' => '请输入标识',
        'name.require'   => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['accord_id'],
        'add'     => ['unique', 'name'],
        'edit'    => ['accord_id', 'unique', 'name'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
    ];

    // 自定义验证规则：协议是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new AccordModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $unique = $data['unique'];
        if (is_numeric($unique)) {
            return '标识不能为纯数字';
        }

        $where[] = [$pk, '<>', $id];
        $where[] = ['unique', '=', $unique];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '标识已存在：' . $unique;
        }

        return true;
    }
}
