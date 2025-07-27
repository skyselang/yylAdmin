<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$validate.namespace};

use think\Validate;
use {$service.namespace}\{$service.class_name} as Service;
use {$tables[0].namespace}\{$tables[0].model_name} as Model;

/**
 * {$form.controller_title}验证器
 */
class {$validate.class_name} extends Validate
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
        'ids' => ['require', 'array'],
        'field' => ['require', 'checkUpdateField'],
        {foreach $tables[0].datas as $k=>$item}
        {if '{$item.check}'}
        '{$item.field}' => ['{foreach $item.check as $j=>$checkItem}{$checkItem.value}{if {$j}<{$count(item.check)}-1}|{/if}{/foreach}',{/if}
        {if !'{$item.check}' && '{$item.not_null}'}
        '{$item.field}' => ['require','checkExisted'],
        {/if}
        {/foreach}
        'is_disable' => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        {foreach $tables[0].datas as $k=>$item}
        {if '{$item.check}'}{foreach $item.check as $j=>$checkItem}'{$item.field}.{$checkItem.value}' => '{$checkItem.message}',
        {/foreach}{/if}
        {/foreach}
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['{$custom.field_pk}'],
        'add'     => [{foreach $tables[0].datas as $k=>$item}{if '{$item.add}'=='true' && ('{$item.not_null}' || '{$item.check}')}'{$item.field}', {/if}{/foreach}],
        'edit'    => [{foreach $tables[0].datas as $k=>$item}{if '{$item.edit}'=='true' && ('{$item.not_null}' || '{$item.check}')}'{$item.field}', {/if}{/foreach}],
        'dele'    => ['ids'],
        'disable' => ['ids', 'is_disable'],
        'update'  => ['ids', 'field'],
    ];

    // 自定义验证规则：是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $unique = $data['unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['unique', '=', $unique]]);
            $info = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $name = $data['name'] ?? '';
        $where = where_delete([[$pk, '<>', $id], ['name', '=', $name]]);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $name;
        }

        return true;
    }

    // 自定义验证规则：批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $field = $data['field'];
        $updateField = $this->service::$updateField;
        if (!in_array($field, $updateField)) {
            return lang('不允许修改的字段：') . $field;
        }

        return true;
    }
}
