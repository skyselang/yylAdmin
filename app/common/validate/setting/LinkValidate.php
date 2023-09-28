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
use app\common\model\setting\LinkModel;

/**
 * 友链管理验证器
 */
class LinkValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'link_id'    => ['require'],
        'name'       => ['require', 'checkExisted'],
        'start_time' => ['dateFormat:Y-m-d H:i:s'],
        'end_time'   => ['dateFormat:Y-m-d H:i:s'],
    ];

    // 错误信息
    protected $message = [
        'name.require'          => '请输入名称',
        'start_time.dateFormat' => '开始时间日期格式：YYYY-MM-DD hh:mm:ss',
        'start_time.dateFormat' => '结束时间日期格式：YYYY-MM-DD hh:mm:ss',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['link_id'],
        'add'      => ['name', 'start_time', 'end_time'],
        'edit'     => ['link_id', 'name', 'start_time', 'end_time'],
        'dele'     => ['ids'],
        'disable'  => ['ids'],
        'datetime' => ['ids', 'start_time', 'end_time'],
    ];

    // 自定义验证规则：友链是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new LinkModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $unique = $data['unique'] ?? '';
        if ($unique) {
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
        }

        return true;
    }
}
