<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\cms;

use app\common\BaseController;
use app\common\validate\cms\CategoryValidate;
use app\common\service\cms\CategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("320")
 */
class Category extends BaseController
{
    /**
     * @Apidoc\Title("内容分类列表")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned("list", ref="app\common\model\cms\CategoryModel\listReturn", type="array", desc="分类列表")
     * @Apidoc\Returned("tree", ref="app\common\model\cms\CategoryModel\treeReturn", type="tree", childrenField="children", desc="分类树形")
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'category_id,category_pid,is_hide,sort');

        if (count($where) > 1) {
            $data['list'] = CategoryService::list('list', $where);
        } else {
            $data['list'] = CategoryService::list('tree', $where);
        }
        $data['tree'] = CategoryService::list('tree', [['is_delete', '=', 0]], [], 'category_id,category_pid,category_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类信息")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\id")
     * @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\infoReturn")
     * @Apidoc\Returned(ref="imgsReturn")
     */
    public function info()
    {
        $param['category_id'] = $this->request->param('category_id/d', '');

        validate(CategoryValidate::class)->scene('info')->check($param);

        $data = CategoryService::info($param['category_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\addParam")
     * @Apidoc\Param("category_name", mock="@ctitle(2, 5)")
     * @Apidoc\Param(ref="imgsParam")
     */
    public function add()
    {
        $param['category_pid']  = $this->request->param('category_pid/d', 0);
        $param['category_name'] = $this->request->param('category_name/s', '');
        $param['title']         = $this->request->param('title/s', '');
        $param['keywords']      = $this->request->param('keywords/s', '');
        $param['description']   = $this->request->param('description/s', '');
        $param['img_id']        = $this->request->param('img_id/d', 0);
        $param['imgs']          = $this->request->param('imgs/a', []);
        $param['sort']          = $this->request->param('sort/d', 250);

        validate(CategoryValidate::class)->scene('add')->check($param);

        $param['img_ids'] = file_ids($param['imgs']);
        $data = CategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\editParam")
     * @Apidoc\Param(ref="imgsParam")
     */
    public function edit()
    {
        $param['category_id']   = $this->request->param('category_id/d', '');
        $param['category_pid']  = $this->request->param('category_pid/d', 0);
        $param['category_name'] = $this->request->param('category_name/s', '');
        $param['title']         = $this->request->param('title/s', '');
        $param['keywords']      = $this->request->param('keywords/s', '');
        $param['description']   = $this->request->param('description/s', '');
        $param['img_id']        = $this->request->param('img_id/d', 0);
        $param['imgs']          = $this->request->param('imgs/a', []);
        $param['sort']          = $this->request->param('sort/d', 250);

        validate(CategoryValidate::class)->scene('edit')->check($param);

        $param['img_ids'] = file_ids($param['imgs']);
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
        $param['ids']     = $this->request->param('ids/a', '');
        $param['recycle'] = 0;

        validate(CategoryValidate::class)->scene('dele')->check($param);

        $data = CategoryService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\category_pid")
     */
    public function pid()
    {
        $param['ids']          = $this->request->param('ids/a', '');
        $param['category_pid'] = $this->request->param('category_pid/d', 0);

        validate(CategoryValidate::class)->scene('pid')->check($param);

        $data = CategoryService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\is_hide")
     */
    public function ishide()
    {
        $param['ids']     = $this->request->param('ids/a', '');
        $param['is_hide'] = $this->request->param('is_hide/d', 0);

        validate(CategoryValidate::class)->scene('ishide')->check($param);

        $data = CategoryService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\cms\CategoryModel\listReturn", type="array", desc="分类列表")
     * @Apidoc\Returned("tree", ref="app\common\model\cms\CategoryModel\treeReturn", type="tree", childrenField="children", desc="分类树形")
     */
    public function recover()
    {
        $where = $this->where(['is_delete', '=', 1], 'category_id,category_pid,is_hide,sort');

        $order = ['delete_time' => 'desc', 'sort' => 'desc'];

        $data['list'] = CategoryService::list('list', $where, $this->order($order));
        $data['tree'] = CategoryService::list('tree', [['is_delete', '=', 1]], [], 'category_id,category_pid,category_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = $this->request->param('ids/a', '');

        validate(CategoryValidate::class)->scene('reco')->check($param);

        $data = CategoryService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids']     = $this->request->param('ids/a', '');
        $param['recycle'] = 1;

        validate(CategoryValidate::class)->scene('dele')->check($param);

        $data = CategoryService::dele($param['ids'], true);

        return success($data);
    }
}
