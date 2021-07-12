<?php
/*
 * @Description  : 内容
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-12
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\CmsValidate;
use app\common\service\CmsService;
use app\common\service\CmsCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Cms
{
    /**
     * @Apidoc\Title("内容分类")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CmsCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = [];
        $list = CmsCategoryService::list('list');
        foreach ($list as $k => $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = CmsCategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CmsModel\indexList")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $name        = Request::param('name/s', '');
        $category_id = Request::param('category_id/d', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($category_id) {
            $where[] = ['category_id', '=', $category_id];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = CmsService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Param(ref="app\common\model\CmsModel\indexInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\CmsModel\info"),
     *      @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\name")
     *      ),
     *      @Apidoc\Returned("next_info", type="object", desc="下一条",
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\name")
     *      )
     * )
     */
    public function info()
    {
        $param['cms_id']      = Request::param('cms_id/d', '');
        $param['category_id'] = Request::param('category_id/d', 0);

        validate(CmsValidate::class)->scene('info')->check($param);

        $data = CmsService::info($param['cms_id']);

        if ($data['is_delete'] == 1) {
            exception('内容已被删除');
        }

        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = CmsService::prev($data['cms_id'], $param['category_id']);
        $data['next_info'] = CmsService::next($data['cms_id'], $param['category_id']);

        return success($data);
    }
}
