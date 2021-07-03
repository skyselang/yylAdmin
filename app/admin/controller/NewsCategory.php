<?php
/*
 * @Description  : 新闻分类控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\NewsCategoryValidate;
use app\common\service\NewsCategoryService;

/**
 * @Apidoc\Title("新闻分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class NewsCategory
{
    /**
     * @Apidoc\Title("新闻分类列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = NewsCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻分类信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsCategoryModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\info")
     * )
     */
    public function info()
    {
        $param['news_category_id'] = Request::param('news_category_id/d', '');

        validate(NewsCategoryValidate::class)->scene('info')->check($param);

        $data = NewsCategoryService::info($param['news_category_id']);
        if ($data['is_delete'] == 1) {
            exception('新闻分类已被删除：' . $param['news_category_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsCategoryModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['news_category_pid'] = Request::param('news_category_pid/d', 0);
        $param['category_name']     = Request::param('category_name/s', '');
        $param['title']             = Request::param('title/s', '');
        $param['keywords']          = Request::param('keywords/s', '');
        $param['description']       = Request::param('description/s', '');
        $param['imgs']              = Request::param('imgs/a', []);
        $param['sort']              = Request::param('sort/d', 200);

        validate(NewsCategoryValidate::class)->scene('add')->check($param);

        $data = NewsCategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsCategoryModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['news_category_id']  = Request::param('news_category_id/d', '');
        $param['news_category_pid'] = Request::param('news_category_pid/d', 0);
        $param['category_name']     = Request::param('category_name/s', '');
        $param['title']             = Request::param('title/s', '');
        $param['keywords']          = Request::param('keywords/s', '');
        $param['description']       = Request::param('description/s', '');
        $param['imgs']              = Request::param('imgs/a', []);
        $param['sort']              = Request::param('sort/d', 200);

        validate(NewsCategoryValidate::class)->scene('edit')->check($param);

        $data = NewsCategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news_category", type="array", require=true, desc="新闻分类列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['news_category'] = Request::param('news_category/a', '');

        validate(NewsCategoryValidate::class)->scene('dele')->check($param);

        $data = NewsCategoryService::dele($param['news_category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻分类上传图片")
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
            validate(NewsCategoryValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(NewsCategoryValidate::class)->scene('video')->check($param);
        } else {
            validate(NewsCategoryValidate::class)->scene('file')->check($param);
        }

        $data = NewsCategoryService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("新闻分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news_category", type="array", require=true, desc="新闻分类列表")
     * @Apidoc\Param(ref="app\common\model\NewsCategoryModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['news_category'] = Request::param('news_category/a', '');
        $param['is_hide']       = Request::param('is_hide/d', 0);

        validate(NewsCategoryValidate::class)->scene('ishide')->check($param);

        $data = NewsCategoryService::ishide($param);

        return success($data);
    }
}
