<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容分类控制器
namespace app\admin\controller\cms;

use think\facade\Request;
use app\common\validate\cms\CategoryValidate;
use app\common\service\cms\CategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("320")
 */
class Category
{
    /**
     * @Apidoc\Title("内容分类列表")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned("list", ref="app\common\model\cms\CategoryModel\listReturn", type="array", desc="列表")
     * @Apidoc\Returned("tree", ref="app\common\model\cms\CategoryModel\listReturn", type="tree", childrenField="children", desc="树形")
     */
    public function list()
    {
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['category_id', 'category_pid', 'is_hide', 'sort'])) {
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
        $param['category_id'] = Request::param('category_id/d', '');

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
        $param['category_pid']  = Request::param('category_pid/d', 0);
        $param['category_name'] = Request::param('category_name/s', '');
        $param['title']         = Request::param('title/s', '');
        $param['keywords']      = Request::param('keywords/s', '');
        $param['description']   = Request::param('description/s', '');
        $param['img_id']        = Request::param('img_id/d', 0);
        $param['imgs']          = Request::param('imgs/a', []);
        $param['sort']          = Request::param('sort/d', 250);

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
        $param['category_id']   = Request::param('category_id/d', '');
        $param['category_pid']  = Request::param('category_pid/d', 0);
        $param['category_name'] = Request::param('category_name/s', '');
        $param['title']         = Request::param('title/s', '');
        $param['keywords']      = Request::param('keywords/s', '');
        $param['description']   = Request::param('description/s', '');
        $param['img_id']        = Request::param('img_id/d', 0);
        $param['imgs']          = Request::param('imgs/a', []);
        $param['sort']          = Request::param('sort/d', 250);

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
        $param['ids']     = Request::param('ids/a', '');
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
        $param['ids']          = Request::param('ids/a', '');
        $param['category_pid'] = Request::param('category_pid/d', 0);

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
        $param['ids']     = Request::param('ids/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

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
     * @Apidoc\Returned("list", ref="app\common\model\cms\CategoryModel\listReturn", type="array", desc="列表")
     * @Apidoc\Returned("tree", ref="app\common\model\cms\CategoryModel\listReturn", type="array", desc="树形")
     */
    public function recover()
    {
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['category_id', 'category_pid', 'is_hide', 'sort'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 1];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = ['delete_time' => 'desc', 'sort' => 'desc'];

        $data['list'] = CategoryService::list('list', $where, $order);
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
        $param['ids'] = Request::param('ids/a', '');

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
        $param['ids']     = Request::param('ids/a', '');
        $param['recycle'] = 1;

        validate(CategoryValidate::class)->scene('dele')->check($param);

        $data = CategoryService::dele($param['ids'], true);

        return success($data);
    }
}
