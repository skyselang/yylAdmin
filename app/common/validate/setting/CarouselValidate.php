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
use app\common\model\setting\CarouselModel;

/**
 * 轮播管理验证器
 */
class CarouselValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'carousel_id' => ['require'],
        'title'       => ['require', 'checkExisted'],
    ];

    // 错误信息
    protected $message = [
        'title.require' => '请输入标题',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['carousel_id'],
        'add'      => ['title'],
        'edit'     => ['carousel_id', 'title'],
        'dele'     => ['ids'],
        'position' => ['ids'],
        'disable'  => ['ids'],
    ];

    // 自定义验证规则：轮播是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new CarouselModel();
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
