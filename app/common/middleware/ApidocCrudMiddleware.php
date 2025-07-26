<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\middleware;

/**
 * 接口文档接口生成器创建Crud中间件
 */
class ApidocCrudMiddleware
{
    // 生成文件及数据表前执行
    public function before($tplParams)
    {
        $field_pk       = 'id';
        $field_list     = [];
        $field_add      = [];
        $field_edit     = [];
        $field_add_edit = [];
        $fields         = $tplParams['tables'][0]['datas'];
        foreach ($fields as $field) {
            if ($field['main_key']) {
                $field_pk = $field['field'];
            }
            if ($field['list'] ?? false) {
                $field_list[] = $field['field'];
            }
            if ($field['add'] ?? false) {
                $field_add[] = $field['field'];
            }
            if ($field['edit'] ?? false) {
                $field_edit[] = $field['field'];
            }
        }
        $field_add_edit = array_unique(array_merge($field_edit, $field_add));
        $custom = [
            'field_pk'       => $field_pk,
            'field_list'     => implode(',', $field_list),
            'field_add'      => implode(',', $field_add),
            'field_edit'     => implode(',', $field_edit),
            'field_add_edit' => $field_add_edit,
        ];
        $tplParams['custom'] = $custom;

        return $tplParams;
    }

    // 生成文件及数据表后执行
    public function after($tplParams) {}
}
