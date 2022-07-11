<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// {$form.controller_title}控制器
namespace {$controller.namespace};

use think\facade\Request;
use {$validate.namespace}\{$validate.class_name} as {$validate.class_name}Validate;
use {$service.namespace}\{$service.class_name} as {$service.class_name}Service;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("{$form.controller_title}")
 * @Apidoc\Group("{$form.group}")
 * @Apidoc\Sort("999")
 */
class {$controller.class_name}
{
    /**
     * @Apidoc\Title("{$form.controller_title}列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="{$tables[0].model_path}\{$tables[0].model_name}\listReturn")
     * )
     */
    public function list()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['id'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 0];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $field = '';

        $data = {$service.class_name}Service::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("{$form.controller_title}信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Param(ref="{$tables[0].model_path}\{$tables[0].model_name}\id")
     * @Apidoc\Returned(ref="{$tables[0].model_path}\{$tables[0].model_name}\infoReturn")
     */
    public function info()
    {
        $param['id'] = Request::param('id/d', '');

        validate({$validate.class_name}Validate::class)->scene('info')->check($param);

        $data = {$service.class_name}Service::info($param['id']);
        if ($data['is_delete'] == 1) {
            exception('{$form.controller_title}已被删除：' . $param['id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("{$form.controller_title}添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="{$tables[0].model_path}\{$tables[0].model_name}\addParam")
     * @Apidoc\Returned(ref="{$tables[0].model_path}\{$tables[0].model_name}\infoReturn")
     */
    public function add()
    {
        $param = Request::param();

        validate({$validate.class_name}Validate::class)->scene('add')->check($param);

        $data = {$service.class_name}Service::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("{$form.controller_title}修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="{$tables[0].model_path}\{$tables[0].model_name}\editParam")
     */
    public function edit()
    {
        $param = Request::param();

        validate({$validate.class_name}Validate::class)->scene('edit')->check($param);

        $data = {$service.class_name}Service::edit($param['id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("{$form.controller_title}删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate({$validate.class_name}Validate::class)->scene('dele')->check($param);

        $data = {$service.class_name}Service::dele($param['ids']);

        return success($data);
    }
}
