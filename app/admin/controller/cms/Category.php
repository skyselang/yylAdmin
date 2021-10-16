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
 * @Apidoc\Sort("999")
 */
class Category
{
    /**
     * @Apidoc\Title("内容分类列表")
     * @Apidoc\Returned("list", type="array", desc="树形列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\list")
     * )
     */
    public function list()
    {
        $data['list'] = CategoryService::list('tree');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类信息")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\id")
     * @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\info")
     * @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\imgs")
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
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\add")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\imgs")
     */
    public function add()
    {
        $param['category_pid']  = Request::param('category_pid/d', 0);
        $param['category_name'] = Request::param('category_name/s', '');
        $param['title']         = Request::param('title/s', '');
        $param['keywords']      = Request::param('keywords/s', '');
        $param['description']   = Request::param('description/s', '');
        $param['imgs']          = Request::param('imgs/a', []);
        $param['sort']          = Request::param('sort/d', 200);

        validate(CategoryValidate::class)->scene('add')->check($param);

        $data = CategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\edit")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\imgs")
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
        $param['sort']          = Request::param('sort/d', 200);

        validate(CategoryValidate::class)->scene('edit')->check($param);

        $data = CategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\category")
     */
    public function dele()
    {
        $param['category'] = Request::param('category/a', '');

        validate(CategoryValidate::class)->scene('dele')->check($param);

        $data = CategoryService::dele($param['category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\category")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\ishide")
     */
    public function ishide()
    {
        $param['category'] = Request::param('category/a', '');
        $param['is_hide']  = Request::param('is_hide/d', 0);

        validate(CategoryValidate::class)->scene('ishide')->check($param);

        $data = CategoryService::ishide($param['category'], $param['is_hide']);

        return success($data);
    }
}
