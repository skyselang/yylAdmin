<?php
/*
 * @Description  : 内容分类控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-13
 */

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
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = CategoryService::list('tree');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\imgs"),
     * )
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
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\add")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\imgs")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
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
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\edit")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\imgs")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
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
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\category")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['category'] = Request::param('category/a', '');

        validate(CategoryValidate::class)->scene('dele')->check($param);

        $data = CategoryService::dele($param['category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容分类上传图片")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="ParamFile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnFile")
     */
    public function upload()
    {
        $param['type'] = Request::param('type/s', 'image');
        $param['file'] = Request::file('file');

        $param[$param['type']] = $param['file'];
        if ($param['type'] == 'image') {
            validate(CategoryValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(CategoryValidate::class)->scene('video')->check($param);
        } else {
            validate(CategoryValidate::class)->scene('file')->check($param);
        }

        $data = CategoryService::upload($param['file'], $param['type']);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("内容分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\category")
     * @Apidoc\Param(ref="app\common\model\cms\CategoryModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
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
