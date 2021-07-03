<?php
/*
 * @Description  : 产品分类控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-01
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\ProductCategoryValidate;
use app\common\service\ProductCategoryService;

/**
 * @Apidoc\Title("产品分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class ProductCategory
{
    /**
     * @Apidoc\Title("产品分类列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProductCategoryModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = ProductCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("产品分类信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProductCategoryModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ProductCategoryModel\info")
     * )
     */
    public function info()
    {
        $param['product_category_id'] = Request::param('product_category_id/d', '');

        validate(ProductCategoryValidate::class)->scene('info')->check($param);

        $data = ProductCategoryService::info($param['product_category_id']);
        if ($data['is_delete'] == 1) {
            exception('产品分类已被删除：' . $param['product_category_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("产品分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProductCategoryModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['product_category_pid'] = Request::param('product_category_pid/d', 0);
        $param['category_name']        = Request::param('category_name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(ProductCategoryValidate::class)->scene('add')->check($param);

        $data = ProductCategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProductCategoryModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['product_category_id']  = Request::param('product_category_id/d', '');
        $param['product_category_pid'] = Request::param('product_category_pid/d', 0);
        $param['category_name']        = Request::param('category_name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(ProductCategoryValidate::class)->scene('edit')->check($param);

        $data = ProductCategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product_category", type="array", require=true, desc="产品分类列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['product_category'] = Request::param('product_category/a', '');

        validate(ProductCategoryValidate::class)->scene('dele')->check($param);

        $data = ProductCategoryService::dele($param['product_category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品分类上传图片")
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
            validate(ProductCategoryValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(ProductCategoryValidate::class)->scene('video')->check($param);
        } else {
            validate(ProductCategoryValidate::class)->scene('file')->check($param);
        }

        $data = ProductCategoryService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("产品分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product_category", type="array", require=true, desc="产品分类列表")
     * @Apidoc\Param(ref="app\common\model\ProductCategoryModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['product_category'] = Request::param('product_category/a', '');
        $param['is_hide']          = Request::param('is_hide/d', 0);

        validate(ProductCategoryValidate::class)->scene('ishide')->check($param);

        $data = ProductCategoryService::ishide($param);

        return success($data);
    }
}
