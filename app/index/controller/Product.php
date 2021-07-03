<?php
/*
 * @Description  : 产品
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\ProductValidate;
use app\common\service\ProductService;
use app\common\service\ProductCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("产品")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Product
{
    /**
     * @Apidoc\Title("分类列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProductCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = [];
        $list = ProductCategoryService::list('list');
        foreach ($list as $k => $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = ProductCategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProductModel\indexList")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $name                = Request::param('name/s', '');
        $product_category_id = Request::param('product_category_id/d', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($product_category_id) {
            $where[] = ['product_category_id', '=', $product_category_id];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = ProductService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("产品信息")
     * @Apidoc\Param(ref="app\common\model\ProductModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ProductModel\info"),
     *      @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\name")
     *      ),
     *      @Apidoc\Returned("next_info", type="object", desc="下一条",
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\ProductModel\name")
     *      )
     * )
     */
    public function info()
    {
        $param['product_id'] = Request::param('product_id/d', '');

        validate(ProductValidate::class)->scene('info')->check($param);

        $data = ProductService::info($param['product_id']);

        if ($data['is_delete'] == 1) {
            exception('产品已被删除');
        }

        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = ProductService::prev($data['product_id']);
        $data['next_info'] = ProductService::next($data['product_id']);

        return success($data);
    }
}
