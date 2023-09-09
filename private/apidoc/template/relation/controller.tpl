<?php
namespace {$controller.namespace};

use app\BaseController;
use app\Request;
use hg\apidoc\annotation as Apidoc;
use {$service.namespace}\{$service.class_name} as {$service.class_name}Service;
use {$validate.namespace}\{$validate.class_name} as {$validate.class_name}Validate;
use think\App;

/**
 * @Apidoc\Title("{$form.controller_title}")
 * @Apidoc\Group("{$form.group}")
 */
class {$controller.class_name} extends BaseController
{
   protected $service;

   protected $validate;

   public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->service = new {$service.class_name}Service();
        $this->validate = new {$validate.class_name}Validate();
    }

    /**
    * @Apidoc\Title("查询分页列表")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/list")
    * @Apidoc\Query(ref="pagingParam")
{foreach $tables[0].datas as $k=>$item}
    {if '{$item.query}'=='true'}* @Apidoc\Query("{$item.field}",type="{$item.type}"{if '{$item.not_null}'=='true'},require=true{/if},desc="{$item.desc}"){/if}
{/foreach}
    * @Apidoc\Returned(ref="pagingParam")
    * @Apidoc\Returned("data",type="array",ref="app\model\{$tables[0].model_name}",withoutField="{foreach $tables[0].datas as $k=>$item}{if '{$item.list}'!='true'}{$item.field},{/if}{/foreach}")
    */
    public function getPageList(Request $request){
        $page = $request->param("pageIndex",config('app.defaultPage'));
        $limit = $request->param("pageSize",config('app.defaultLimit'));
        $where = [];
        $res = $this->service->getPageList($page,$limit,$where);
        return show(config("httpCode.success"),"",$res);
    }

    /**
    * @Apidoc\Title("根据主键查询明细")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/detail")
{foreach $tables[0].datas as $k=>$item}
    {if '{$item.main_key}'=='true'}* @Apidoc\Query("{$item.field}",type="{$item.type}"{if '{$item.not_null}'=='true'},require=true{/if},desc="{$item.desc}"){/if}
{/foreach}
    * @Apidoc\Returned(ref="app\model\{$tables[0].model_name}",withoutField="{foreach $tables[0].datas as $k=>$item}{if '{$item.detail}'!='true'}{$item.field},{/if}{/foreach}")
    */
    public function detail(Request $request){
        $params = $request->param();
        $res = $this->service->getInfoById($params['id']);
        return show(config("httpCode.success"),"",$res);
    }

    /**
    * @Apidoc\Title("新增")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/add")
    * @Apidoc\Method("POST")
    * @Apidoc\Param (ref="app\model\{$tables[0].model_name}",withoutField="{foreach $tables[0].datas as $k=>$item}{if '{$item.add}'!='true'}{$item.field},{/if}{/foreach}")
    * @Apidoc\Returned (ref="app\model\{$tables[0].model_name}")
    */
    public function add(Request $request){
        if (!empty($this->validate)){
            $this->validate->goCheck('add');
        }
        $params = $request->post();
        $res = $this->service->add($params);
        return show(config("httpCode.success"),"",$res);
    }


    /**
    * @Apidoc\Title("编辑")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/edit")
    * @Apidoc\Method("PUT")
    * @Apidoc\Param (ref="app\model\{$tables[0].model_name}",withoutField="{foreach $tables[0].datas as $k=>$item}{if '{$item.edit}'!='true'}{$item.field},{/if}{/foreach}")
    * @Apidoc\Returned (ref="app\model\{$tables[0].model_name}")
    */
    public function edit(Request $request){
        if (!empty($this->validate)){
            $this->validate->goCheck('edit');
        }
        $params = $request->post();
        $res = $this->service->update($params);
        return show(config("httpCode.success"),"",$res);
    }



    /**
    * @Apidoc\Title("删除")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/delete")
    * @Apidoc\Method("DELETE")
{foreach $tables[0].datas as $k=>$item}
    {if '{$item.main_key}'=='true'}* @Apidoc\Param("{$item.field}",type="{$item.type}"{if '{$item.not_null}'=='true'},require=true{/if},desc="{$item.desc}"){/if}
{/foreach}
    * @Apidoc\Returned("data",type="boolean",desc="删除状态")
    */
    public function delete(Request $request){
        $params = $request->post();
        $res = $this->service->delete($params['id']);
        return show(config("httpCode.success"),"",$res);
    }

    /**
    * @Apidoc\Title("添加{$tables[1].table_comment}记录")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/add{$tables[1].model_name}")
    * @Apidoc\Method("POST")
    * @Apidoc\Param (ref="app\model\{$tables[1].model_name}",withoutField="id")
    * @Apidoc\Returned (ref="app\model\{$tables[1].model_name}")
    */
    public function add{$tables[1].model_name}(Request $request){
        if (!empty($this->validate)){
            $this->validate->goCheck('add{$tables[1].model_name}');
        }
        $params = $request->post();
        $res = $this->service->add{$tables[1].model_name}($params);
        return show(config("httpCode.success"),"",$res);
    }

    /**
    * @Apidoc\Title("根据{$tables[0].table_comment}id查询{$tables[1].table_comment}记录")
    * @Apidoc\Url("/{$app[0].folder}{if '{$app[1].folder}'}/{$app[1].folder}{/if}/{$lcfirst(controller.class_name)}/get{$tables[1].model_name}By{$form.relation_field}")
    * @Apidoc\Method("GET")
    * @Apidoc\Query ("{$form.relation_field}",type="int",desc="{$tables[0].table_comment}id")
    * @Apidoc\Returned(ref="pagingParam")
    * @Apidoc\Returned (ref="app\model\{$tables[1].model_name}",type="array")
    */
    public function get{$tables[1].model_name}By{$form.relation_field}(Request $request){
        if (!empty($this->validate)){
            $this->validate->goCheck('add{$tables[1].model_name}');
        }
        $page = $request->param("pageIndex",config('app.defaultPage'));
        $limit = $request->param("pageSize",config('app.defaultLimit'));
        $params = $request->param();
        $res = $this->service->get{$tables[1].model_name}By{$form.relation_field}($page,$limit,$params);
        return show(config("httpCode.success"),"",$res);
    }


}

