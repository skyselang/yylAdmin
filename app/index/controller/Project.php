<?php
/*
 * @Description  : 案例
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\ProjectValidate;
use app\common\service\ProjectService;
use app\common\service\ProjectCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("案例")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Project
{
    /**
     * @Apidoc\Title("分类列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProjectCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = [];
        $list = ProjectCategoryService::list('list');
        foreach ($list as $k => $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = ProjectCategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\indexList")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\list")
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
        $project_category_id = Request::param('project_category_id/d', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($project_category_id) {
            $where[] = ['project_category_id', '=', $project_category_id];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = ProjectService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例信息")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ProjectModel\info"),
     *      @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\name")
     *      ),
     *      @Apidoc\Returned("next_info", type="object", desc="下一条",
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\name")
     *      )
     * )
     */
    public function info()
    {
        $param['project_id'] = Request::param('project_id/d', '');

        validate(ProjectValidate::class)->scene('info')->check($param);

        $data = ProjectService::info($param['project_id']);

        if ($data['is_delete'] == 1) {
            exception('案例已被删除');
        }

        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = ProjectService::prev($data['project_id']);
        $data['next_info'] = ProjectService::next($data['project_id']);

        return success($data);
    }
}
