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
     * @Apidoc\Returned("list", type="array", desc="树形列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\listReturn")
     * )
     */
    public function list()
    {
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');

        $where = [];
        if ($search_field && $search_value) {
            if (in_array($search_field, ['category_id', 'category_pid'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['is_hide'])) {
                if ($search_value == '是' || $search_value == '1') {
                    $search_value = 1;
                } else {
                    $search_value = 0;
                }
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }

        $data['list'] = CategoryService::list('tree', $where);

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
        if ($data['is_delete'] == 1) {
            exception('内容分类已被删除：' . $param['category_id']);
        }

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
        $param['imgs']          = Request::param('imgs/a', []);
        $param['sort']          = Request::param('sort/d', 250);

        validate(CategoryValidate::class)->scene('add')->check($param);

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
        $param['imgs']          = Request::param('imgs/a', []);
        $param['sort']          = Request::param('sort/d', 250);

        validate(CategoryValidate::class)->scene('edit')->check($param);

        $data = CategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

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

        $data = CategoryService::pid($param['ids'], $param['category_pid']);

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

        $data = CategoryService::ishide($param['ids'], $param['is_hide']);

        return success($data);
    }
}
