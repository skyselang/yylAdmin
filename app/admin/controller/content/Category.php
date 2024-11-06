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
     * @Apidoc\Returned("list", type="tree", desc="分类树形", children={
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel", field="category_id,category_pid,category_name,category_unique,image_id,sort,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel\getImageUrlAttr", field="image_url"),
     * })
     * @Apidoc\Returned("tree", ref="app\common\model\content\CategoryModel", type="tree", desc="分类树形", field="category_id,category_pid,category_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data['list'] = CategoryService::list('tree', $where);
        $data['exps'] = where_exps();
        $data['tree'] = CategoryService::list('tree', [where_delete()], [], 'category_pid,category_name');
        $data['count'] = count(CategoryService::list('list', $where));
        if (count($where) > 1) {
            $list = tree_to_list($data['list']);
            $all  = tree_to_list($data['tree']);
            $pk   = 'category_id';
            $pid  = 'category_pid';
            $ids  = [];
            foreach ($list as $val) {
                $pids = children_parent_ids($all, $val[$pk], $pk, $pid);
                $cids = parent_children_ids($all, $val[$pk], $pk, $pid);
                $ids  = array_merge($ids, $pids, $cids);
            }
            $data['list'] = CategoryService::list('tree', [[$pk, 'in', $ids], where_delete()]);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类信息")
     * @Apidoc\Query(ref="app\common\model\content\CategoryModel", field="category_id")
     * @Apidoc\Returned(ref="app\common\model\content\CategoryModel")
     * @Apidoc\Returned(ref="app\common\model\content\CategoryModel\getImageUrlAttr", field="image_url")
     * @Apidoc\Returned(ref="imagesReturn")
     */
    public function info()
    {
        $param = $this->params(['category_id/d' => '']);

        validate(CategoryValidate::class)->scene('info')->check($param);

        $data = CategoryService::info($param['category_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="category_pid,category_name,category_unique,image_id,title,keywords,description,sort,remark")
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
     * @Apidoc\Param(ref="app\common\model\content\CategoryModel", field="category_id,category_pid,category_name,category_unique,image_id,title,keywords,description,sort,remark")
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
        $param = $this->params(['ids/a' => []]);

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
        $param = $this->params(['ids/a' => [], 'category_pid' => 0]);

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
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(CategoryValidate::class)->scene('disable')->check($param);

        $data = CategoryService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类内容列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\content\CategoryModel", field="category_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="内容列表", children={
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,image_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getImageUrlAttr", field="image_url"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr", field="category_names"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr", field="tag_names"),
     * })
     */
    public function contentList()
    {
        $param = $this->params(['category_id/d' => '']);

        validate(CategoryValidate::class)->scene('content')->check($param);

        $where = $this->where(where_delete(['category_ids', 'in', [$param['category_id']]]));

        $data = CategoryService::content($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类内容解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("category_id", type="array", require=true, desc="分类id")
     * @Apidoc\Param("content_ids", type="array", require=false, desc="内容id，为空则解除所有内容")
     */
    public function contentRemove()
    {
        $param = $this->params(['category_id/a' => [], 'content_ids/a' => []]);

        validate(CategoryValidate::class)->scene('contentRemove')->check($param);

        $data = CategoryService::contentRemove($param['category_id'], $param['content_ids']);

        return success($data);
    }
}
