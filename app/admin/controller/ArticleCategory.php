<?php
/*
 * @Description  : 文章分类控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-01
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\ArticleCategoryValidate;
use app\common\service\ArticleCategoryService;

/**
 * @Apidoc\Title("文章分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class ArticleCategory
{
    /**
     * @Apidoc\Title("文章分类列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ArticleCategoryModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = ArticleCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("文章分类信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ArticleCategoryModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ArticleCategoryModel\info")
     * )
     */
    public function info()
    {
        $param['article_category_id'] = Request::param('article_category_id/d', '');

        validate(ArticleCategoryValidate::class)->scene('info')->check($param);

        $data = ArticleCategoryService::info($param['article_category_id']);
        if ($data['is_delete'] == 1) {
            exception('文章分类已被删除：' . $param['article_category_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("文章分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ArticleCategoryModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['article_category_pid'] = Request::param('article_category_pid/d', 0);
        $param['category_name']        = Request::param('category_name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(ArticleCategoryValidate::class)->scene('add')->check($param);

        $data = ArticleCategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ArticleCategoryModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['article_category_id']  = Request::param('article_category_id/d', '');
        $param['article_category_pid'] = Request::param('article_category_pid/d', 0);
        $param['category_name']        = Request::param('category_name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(ArticleCategoryValidate::class)->scene('edit')->check($param);

        $data = ArticleCategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article_category", type="array", require=true, desc="文章分类列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['article_category'] = Request::param('article_category/a', '');

        validate(ArticleCategoryValidate::class)->scene('dele')->check($param);

        $data = ArticleCategoryService::dele($param['article_category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章分类上传图片")
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
            validate(ArticleCategoryValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(ArticleCategoryValidate::class)->scene('video')->check($param);
        } else {
            validate(ArticleCategoryValidate::class)->scene('file')->check($param);
        }

        $data = ArticleCategoryService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("文章分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article_category", type="array", require=true, desc="文章分类列表")
     * @Apidoc\Param(ref="app\common\model\ArticleCategoryModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['article_category'] = Request::param('article_category/a', '');
        $param['is_hide']          = Request::param('is_hide/d', 0);

        validate(ArticleCategoryValidate::class)->scene('ishide')->check($param);

        $data = ArticleCategoryService::ishide($param);

        return success($data);
    }
}
