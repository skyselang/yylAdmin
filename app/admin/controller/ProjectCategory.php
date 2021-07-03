<?php
/*
 * @Description  : 案例分类控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-01
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\ProjectCategoryValidate;
use app\common\service\ProjectCategoryService;

/**
 * @Apidoc\Title("案例分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class ProjectCategory
{
    /**
     * @Apidoc\Title("案例分类列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ProjectCategoryModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = ProjectCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("案例分类信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProjectCategoryModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ProjectCategoryModel\info")
     * )
     */
    public function info()
    {
        $param['project_category_id'] = Request::param('project_category_id/d', '');

        validate(ProjectCategoryValidate::class)->scene('info')->check($param);

        $data = ProjectCategoryService::info($param['project_category_id']);
        if ($data['is_delete'] == 1) {
            exception('案例分类已被删除：' . $param['project_category_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("案例分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProjectCategoryModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['project_category_pid'] = Request::param('project_category_pid/d', 0);
        $param['category_name']        = Request::param('category_name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(ProjectCategoryValidate::class)->scene('add')->check($param);

        $data = ProjectCategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ProjectCategoryModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['project_category_id']  = Request::param('project_category_id/d', '');
        $param['project_category_pid'] = Request::param('project_category_pid/d', 0);
        $param['category_name']        = Request::param('category_name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(ProjectCategoryValidate::class)->scene('edit')->check($param);

        $data = ProjectCategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project_category", type="array", require=true, desc="案例分类列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['project_category'] = Request::param('project_category/a', '');

        validate(ProjectCategoryValidate::class)->scene('dele')->check($param);

        $data = ProjectCategoryService::dele($param['project_category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("案例分类上传图片")
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
            validate(ProjectCategoryValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(ProjectCategoryValidate::class)->scene('video')->check($param);
        } else {
            validate(ProjectCategoryValidate::class)->scene('file')->check($param);
        }

        $data = ProjectCategoryService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("案例分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("project_category", type="array", require=true, desc="案例分类列表")
     * @Apidoc\Param(ref="app\common\model\ProjectCategoryModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['project_category'] = Request::param('project_category/a', '');
        $param['is_hide']          = Request::param('is_hide/d', 0);

        validate(ProjectCategoryValidate::class)->scene('ishide')->check($param);

        $data = ProjectCategoryService::ishide($param);

        return success($data);
    }
}
