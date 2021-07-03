<?php
/*
 * @Description  : 产品管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\ProductValidate;
use app\common\service\ProductService;
use app\common\service\ProductCategoryService;

/**
 * @Apidoc\Title("产品管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Product
{
    /**
     * @Apidoc\Title("产品分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProductCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = ProductCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("产品列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProductModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\ProductCategoryModel\category_name")
     *      )
     * )
     */
    public function list()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $product_id          = Request::param('product_id/d', '');
        $name                = Request::param('name/s', '');
        $product_category_id = Request::param('product_category_id/d', '');
        $date_type           = Request::param('date_type/s', '');
        $date_range          = Request::param('date_range/a', []);

        validate(ProductValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($product_id) {
            $where[] = ['product_id', '=', $product_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($product_category_id) {
            $where[] = ['product_category_id', '=', $product_category_id];
        }
        if ($date_type && $date_range) {
            $where[] = [$date_type, '>=', $date_range[0] . ' 00:00:00'];
            $where[] = [$date_type, '<=', $date_range[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = ProductService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProductModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ProductModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\ProductModel\imgfile")
     * )
     */
    public function info()
    {
        $param['product_id'] = Request::param('product_id/d', '');

        validate(ProductValidate::class)->scene('info')->check($param);

        $data = ProductService::info($param['product_id']);
        if ($data['is_delete'] == 1) {
            exception('产品已被删除：' . $param['product_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("产品添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProductModel\add")
     * @Apidoc\Param(ref="app\common\model\ProductModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['product_category_id'] = Request::param('product_category_id/d', '');
        $param['name']                = Request::param('name/s', '');
        $param['title']               = Request::param('title/s', '');
        $param['keywords']            = Request::param('keywords/s', '');
        $param['description']         = Request::param('description/s', '');
        $param['imgs']                = Request::param('imgs/a', []);
        $param['content']             = Request::param('content/s', '');
        $param['files']               = Request::param('files/a', []);
        $param['sort']                = Request::param('sort/d', 200);

        validate(ProductValidate::class)->scene('add')->check($param);

        $data = ProductService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProductModel\edit")
     * @Apidoc\Param(ref="app\common\model\ProductModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['product_id']          = Request::param('product_id/d', '');
        $param['product_category_id'] = Request::param('product_category_id/d', '');
        $param['name']                = Request::param('name/s', '');
        $param['title']               = Request::param('title/s', '');
        $param['keywords']            = Request::param('keywords/s', '');
        $param['description']         = Request::param('description/s', '');
        $param['imgs']                = Request::param('imgs/a', []);
        $param['content']             = Request::param('content/s', '');
        $param['files']               = Request::param('files/a', []);
        $param['sort']                = Request::param('sort/d', 200);

        validate(ProductValidate::class)->scene('edit')->check($param);

        $data = ProductService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['product'] = Request::param('product/a', '');

        validate(ProductValidate::class)->scene('dele')->check($param);

        $data = ProductService::dele($param['product']);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品上传文件")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="ParamFile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnFile")
     */
    public function upload()
    {
        $param['type'] = Request::param('type/s', 'file');
        $param['file'] = Request::file('file');

        $param[$param['type']] = $param['file'];
        if ($param['type'] == 'image') {
            validate(ProductValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(ProductValidate::class)->scene('video')->check($param);
        } else {
            validate(ProductValidate::class)->scene('file')->check($param);
        }

        $data = ProductService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("产品是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Param(ref="app\common\model\ProductModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['product'] = Request::param('product/a', '');
        $param['is_top']  = Request::param('is_top/d', 0);

        validate(ProductValidate::class)->scene('istop')->check($param);

        $data = ProductService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Param(ref="app\common\model\ProductModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['product'] = Request::param('product/a', '');
        $param['is_hot']  = Request::param('is_hot/d', 0);

        validate(ProductValidate::class)->scene('ishot')->check($param);

        $data = ProductService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Param(ref="app\common\model\ProductModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['product'] = Request::param('product/a', '');
        $param['is_rec']  = Request::param('is_rec/d', 0);

        validate(ProductValidate::class)->scene('isrec')->check($param);

        $data = ProductService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Param(ref="app\common\model\ProductModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['product'] = Request::param('product/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(ProductValidate::class)->scene('ishide')->check($param);

        $data = ProductService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProductModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\ProductCategoryModel\category_name")
     *      )
     * )
     */
    public function recover()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $product_id          = Request::param('product_id/d', '');
        $name                = Request::param('name/s', '');
        $product_category_id = Request::param('product_category_id/d', '');
        $date_type           = Request::param('date_type/s', '');
        $date_range          = Request::param('date_range/a', []);

        validate(ProductValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($product_id) {
            $where[] = ['product_id', '=', $product_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($product_category_id !== '') {
            $where[] = ['product_category_id', '=', $product_category_id];
        }
        if ($date_type && $date_range) {
            $where[] = [$date_type, '>=', $date_range[0] . ' 00:00:00'];
            $where[] = [$date_type, '<=', $date_range[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        } else {
            $order = ['delete_time' => 'desc'];
        }

        $field = 'delete_time';

        $data = ProductService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['product'] = Request::param('product/a', '');

        validate(ProductValidate::class)->scene('dele')->check($param);

        $data = ProductService::recoverReco($param['product']);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("product", type="array", require=true, desc="产品列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['product'] = Request::param('product/a', '');

        validate(ProductValidate::class)->scene('dele')->check($param);

        $data = ProductService::recoverDele($param['product']);

        return success($data);
    }
}
