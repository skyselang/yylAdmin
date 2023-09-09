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

/**
 * {$form.controller_title}验证器
 */
class {$validate.class_name} extends Validate
{
    // 验证规则
    protected $rule = [
        'ids' => ['require', 'array'],
        {foreach $tables[0].datas as $k=>$item}
        {if '{$item.check}'}
        '{$item.field}' => '{foreach $item.check as $j=>$checkItem}{$checkItem.value}{if {$j}<{$count(item.check)}-1}|{/if}{/foreach}',{/if}
        {if !'{$item.check}' && '{$item.not_null}'}
        '{$item.field}' => 'require',
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
        'info'    =>  ['{$custom.field_pk}'],
        'add'     =>  [{foreach $tables[0].datas as $k=>$item}{if '{$item.add}'=='true' && ('{$item.not_null}' || '{$item.check}')}'{$item.field}', {/if}{/foreach}],
        'edit'    =>  [{foreach $tables[0].datas as $k=>$item}{if '{$item.edit}'=='true' && ('{$item.not_null}' || '{$item.check}')}'{$item.field}', {/if}{/foreach}],
        'dele'    =>  ['ids'],
        'disable' =>  ['ids', 'is_disable'],
    ];
}
