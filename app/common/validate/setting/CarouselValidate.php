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
use app\common\service\setting\CarouselService as Service;
use app\common\model\setting\CarouselModel as Model;

/**
 * 轮播管理验证器
 */
class CarouselValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'field'       => ['require', 'checkUpdateField'],
        'carousel_id' => ['require'],
        'title'       => ['require', 'checkExisted'],
    ];

    // 错误信息
    protected $message = [
        'title.require' => '请输入标题',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['carousel_id'],
        'add'     => ['title'],
        'edit'    => ['carousel_id', 'title'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
        'update'  => ['ids', 'field'],
    ];

    // 自定义验证规则：轮播是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], ['title', '=', $data['title']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('标题已存在：') . $data['title'];
        }

        return true;
    }

    // 自定义验证规则：轮播批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }
}
