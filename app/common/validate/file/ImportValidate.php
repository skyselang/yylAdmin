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
use app\common\service\file\ImportService as Service;
use app\common\model\file\ImportModel as Model;

/**
 * 导入文件验证器
 */
class ImportValidate extends Validate
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
        'ids'       => ['require', 'array'],
        'field'     => ['require', 'checkUpdateField'],
        'import_id' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'update' => ['field'],
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['import_id'],
        'edit'        => ['import_id'],
        'dele'        => ['ids'],
        'disable'     => ['ids'],
        'update'      => ['ids', 'field'],
        'recycleReco' => ['ids'],
        'recycleDele' => ['ids'],
    ];

    // 自定义验证规则：导入文件批量修改字段
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
