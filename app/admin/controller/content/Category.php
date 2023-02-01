<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\content;

use app\common\controller\BaseController;
use app\common\validate\content\CategoryValidate;
use app\common\service\content\CategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容分类")
 * @Apidoc\Group("content")
 * @Apidoc\Sort("200")
 */
class Category extends BaseController
{
    /**
     * @Apidoc\Title("内容分类列表")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned("list", ref="app\common\model\content\CategoryModel", type="tree", desc="分类树形", field="category_id,category_pid,category_name,category_unique,cover_id,sort,is_disable,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel\getCoverUrlAttr"),
     * )
     * @Apidoc\Returned("tree", ref="app\common\model\content\CategoryModel", type="tree", desc="分类树形", field="category_id,category_pid,category_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data['list']  = CategoryService::list('tree', $where, $this->order());
        $data['tree']  = CategoryService::list('tree', [where_delete()], [], 'category_id,category_pid,category_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类信息")
     * @Apidoc\Query(ref="app\common\model\content\CategoryModel", field="category_id")
     * @Apidoc\Returned(ref="app\common\model\content\CategoryModel")
     * @Apidoc\Returned(ref="imagesReturn")
     */
    public function info()
    {
        $param['category_id'] = $this->request->param('category_id/d', 0);

        validate(CategoryValidate::class)->scene('info')->check($param);

        $data = CategoryService::info($param['category_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="category_pid,category_name,category_unique,cover_id,title,keywords,description,sort")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function add()
    {
        $param = $this->params(CategoryService::$edit_field);
 
        validate(CategoryValidate::class)->scene('add')->check($param);

        $data = CategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="category_id,category_pid,category_name,category_unique,cover_id,title,keywords,description,sort")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function edit()
    {
        $param = $this->params(CategoryService::$edit_field);

        validate(CategoryValidate::class)->scene('edit')->check($param);

        $data = CategoryService::edit($param['category_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(CategoryValidate::class)->scene('dele')->check($param);

        $data = CategoryService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="category_pid")
     */
    public function editpid()
    {
        $param['ids']          = $this->request->param('ids/a', []);
        $param['category_pid'] = $this->request->param('category_pid/d', 0);

        validate(CategoryValidate::class)->scene('editpid')->check($param);

        $data = CategoryService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(CategoryValidate::class)->scene('disable')->check($param);

        $data = CategoryService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类内容")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\content\CategoryModel", field="category_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\content\ContentModel", type="array", desc="内容列表", field="content_id,cover_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCoverUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr"),
     * )
     */
    public function content()
    {
        $param['category_id'] = $this->request->param('category_id/d', 0);

        validate(CategoryValidate::class)->scene('content')->check($param);

        $where = $this->where(where_delete(['category_ids', 'in', [$param['category_id']]]));

        $data = CategoryService::content($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类内容解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="category_id")
     * @Apidoc\Param("content_ids", type="array", require=false, desc="内容id，为空则解除所有内容")
     */
    public function contentRemove()
    {
        $param['category_id'] = $this->request->param('category_id/a', []);
        $param['content_ids'] = $this->request->param('content_ids/a', []);

        validate(CategoryValidate::class)->scene('contentRemove')->check($param);

        $data = CategoryService::contentRemove($param['category_id'], $param['content_ids']);

        return success($data);
    }
}
