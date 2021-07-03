<?php
/*
 * @Description  : 案例管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\ProjectValidate;
use app\common\service\ProjectService;
use app\common\service\ProjectCategoryService;

/**
 * @Apidoc\Title("案例管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Project
{
    /**
     * @Apidoc\Title("案例分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProjectCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = ProjectCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("案例列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\ProjectCategoryModel\category_name")
     *      )
     * )
     */
    public function list()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $project_id          = Request::param('project_id/d', '');
        $name                = Request::param('name/s', '');
        $project_category_id = Request::param('project_category_id/d', '');
        $date_type           = Request::param('date_type/s', '');
        $date_range          = Request::param('date_range/a', []);

        validate(ProjectValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($project_id) {
            $where[] = ['project_id', '=', $project_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($project_category_id) {
            $where[] = ['project_category_id', '=', $project_category_id];
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

        $data = ProjectService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ProjectModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\ProjectModel\imgfile")
     * )
     */
    public function info()
    {
        $param['project_id'] = Request::param('project_id/d', '');

        validate(ProjectValidate::class)->scene('info')->check($param);

        $data = ProjectService::info($param['project_id']);
        if ($data['is_delete'] == 1) {
            exception('案例已被删除：' . $param['project_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("案例添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\add")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['project_category_id'] = Request::param('project_category_id/d', '');
        $param['name']                = Request::param('name/s', '');
        $param['title']               = Request::param('title/s', '');
        $param['keywords']            = Request::param('keywords/s', '');
        $param['description']         = Request::param('description/s', '');
        $param['imgs']                = Request::param('imgs/a', []);
        $param['content']             = Request::param('content/s', '');
        $param['files']               = Request::param('files/a', []);
        $param['sort']                = Request::param('sort/d', 200);

        validate(ProjectValidate::class)->scene('add')->check($param);

        $data = ProjectService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\edit")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['project_id']          = Request::param('project_id/d', '');
        $param['project_category_id'] = Request::param('project_category_id/d', '');
        $param['name']                = Request::param('name/s', '');
        $param['title']               = Request::param('title/s', '');
        $param['keywords']            = Request::param('keywords/s', '');
        $param['description']         = Request::param('description/s', '');
        $param['imgs']                = Request::param('imgs/a', []);
        $param['content']             = Request::param('content/s', '');
        $param['files']               = Request::param('files/a', []);
        $param['sort']                = Request::param('sort/d', 200);

        validate(ProjectValidate::class)->scene('edit')->check($param);

        $data = ProjectService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['project'] = Request::param('project/a', '');

        validate(ProjectValidate::class)->scene('dele')->check($param);

        $data = ProjectService::dele($param['project']);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例上传文件")
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
            validate(ProjectValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(ProjectValidate::class)->scene('video')->check($param);
        } else {
            validate(ProjectValidate::class)->scene('file')->check($param);
        }

        $data = ProjectService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("案例是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['project'] = Request::param('project/a', '');
        $param['is_top']  = Request::param('is_top/d', 0);

        validate(ProjectValidate::class)->scene('istop')->check($param);

        $data = ProjectService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['project'] = Request::param('project/a', '');
        $param['is_hot']  = Request::param('is_hot/d', 0);

        validate(ProjectValidate::class)->scene('ishot')->check($param);

        $data = ProjectService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['project'] = Request::param('project/a', '');
        $param['is_rec']  = Request::param('is_rec/d', 0);

        validate(ProjectValidate::class)->scene('isrec')->check($param);

        $data = ProjectService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['project'] = Request::param('project/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(ProjectValidate::class)->scene('ishide')->check($param);

        $data = ProjectService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProjectModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProjectModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\ProjectCategoryModel\category_name")
     *      )
     * )
     */
    public function recover()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $project_id          = Request::param('project_id/d', '');
        $name                = Request::param('name/s', '');
        $project_category_id = Request::param('project_category_id/d', '');
        $date_type           = Request::param('date_type/s', '');
        $date_range          = Request::param('date_range/a', []);

        validate(ProjectValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($project_id) {
            $where[] = ['project_id', '=', $project_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($project_category_id !== '') {
            $where[] = ['project_category_id', '=', $project_category_id];
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

        $data = ProjectService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['project'] = Request::param('project/a', '');

        validate(ProjectValidate::class)->scene('dele')->check($param);

        $data = ProjectService::recoverReco($param['project']);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project", type="array", require=true, desc="案例列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['project'] = Request::param('project/a', '');

        validate(ProjectValidate::class)->scene('dele')->check($param);

        $data = ProjectService::recoverDele($param['project']);

        return success($data);
    }
}
