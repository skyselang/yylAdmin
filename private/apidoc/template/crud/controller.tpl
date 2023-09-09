<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$controller.namespace};

use app\common\controller\BaseController;
use {$service.namespace}\{$service.class_name};
use {$validate.namespace}\{$validate.class_name};
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("{$form.controller_title}")
 * @Apidoc\Group("{$form.group}")
 * @Apidoc\Sort("250")
 */
class {$controller.class_name} extends BaseController
{
    /**
    * @Apidoc\Title("{$form.controller_title}列表")
    * @Apidoc\Query(ref="pagingQuery")
    * @Apidoc\Query(ref="sortQuery")
    * @Apidoc\Query(ref="searchQuery")
    * @Apidoc\Query(ref="dateQuery")
    * @Apidoc\Returned(ref="expsReturn")
    * @Apidoc\Returned(ref="pagingReturn")
    * @Apidoc\Returned("list", type="array", desc="{$form.controller_title}列表", children={
    *   @Apidoc\Returned(ref="{$tables[0].namespace}\{$tables[0].model_name}", field="{$custom.field_list}")
    * })
    */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = {$service.class_name}::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
    * @Apidoc\Title("{$form.controller_title}信息")
    * @Apidoc\Query(ref="{$tables[0].namespace}\{$tables[0].model_name}", field="{$custom.field_pk}")
    * @Apidoc\Returned(ref="{$tables[0].namespace}\{$tables[0].model_name}")
    */
    public function info()
    {
        $param = $this->params(['{$custom.field_pk}/d' => '']);

        validate({$validate.class_name}::class)->scene('info')->check($param);

        $data = {$service.class_name}::info($param['{$custom.field_pk}']);

        return success($data);
    }

    /**
    * @Apidoc\Title("{$form.controller_title}添加")
    * @Apidoc\Method("POST")
    * @Apidoc\Param(ref="{$tables[0].namespace}\{$tables[0].model_name}", field="{$custom.field_add}")
    */
    public function add()
    {
        $param = $this->params({$service.class_name}::$edit_field);

        validate({$validate.class_name}::class)->scene('add')->check($param);

        $data = {$service.class_name}::add($param);

        return success($data);
    }

    /**
    * @Apidoc\Title("{$form.controller_title}修改")
    * @Apidoc\Method("POST")
    * @Apidoc\Param(ref="{$tables[0].namespace}\{$tables[0].model_name}", field="{$custom.field_edit}")
    */
    public function edit()
    {
        $param = $this->params({$service.class_name}::$edit_field);

        validate({$validate.class_name}::class)->scene('edit')->check($param);

        $data = {$service.class_name}::edit($param['{$custom.field_pk}'], $param);

        return success($data);
    }

    /**
    * @Apidoc\Title("{$form.controller_title}删除")
    * @Apidoc\Method("POST")
    * @Apidoc\Param(ref="idsParam")
    */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate({$validate.class_name}::class)->scene('dele')->check($param);

        $data = {$service.class_name}::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("{$form.controller_title}禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="{$tables[0].namespace}\{$tables[0].model_name}", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate({$validate.class_name}::class)->scene('disable')->check($param);

        $data = {$service.class_name}::edit($param['ids'], $param);

        return success($data);
    }
}
