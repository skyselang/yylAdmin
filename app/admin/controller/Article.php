<?php
/*
 * @Description  : 文章管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\ArticleValidate;
use app\common\service\ArticleService;
use app\common\service\ArticleCategoryService;

/**
 * @Apidoc\Title("文章管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Article
{
    /**
     * @Apidoc\Title("文章分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ArticleCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = ArticleCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("文章列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\ArticleCategoryModel\category_name")
     *      )
     * )
     */
    public function list()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $article_id          = Request::param('article_id/d', '');
        $name                = Request::param('name/s', '');
        $article_category_id = Request::param('article_category_id/d', '');
        $date_type           = Request::param('date_type/s', '');
        $date_range          = Request::param('date_range/a', []);

        validate(ArticleValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($article_id) {
            $where[] = ['article_id', '=', $article_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($article_category_id) {
            $where[] = ['article_category_id', '=', $article_category_id];
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

        $data = ArticleService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ArticleModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\ArticleModel\imgfile")
     * )
     */
    public function info()
    {
        $param['article_id'] = Request::param('article_id/d', '');

        validate(ArticleValidate::class)->scene('info')->check($param);

        $data = ArticleService::info($param['article_id']);
        if ($data['is_delete'] == 1) {
            exception('文章已被删除：' . $param['article_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("文章添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\add")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['article_category_id'] = Request::param('article_category_id/d', '');
        $param['name']                = Request::param('name/s', '');
        $param['title']               = Request::param('title/s', '');
        $param['keywords']            = Request::param('keywords/s', '');
        $param['description']         = Request::param('description/s', '');
        $param['imgs']                = Request::param('imgs/a', []);
        $param['content']             = Request::param('content/s', '');
        $param['files']               = Request::param('files/a', []);
        $param['sort']                = Request::param('sort/d', 200);

        validate(ArticleValidate::class)->scene('add')->check($param);

        $data = ArticleService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\edit")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['article_id']          = Request::param('article_id/d', '');
        $param['article_category_id'] = Request::param('article_category_id/d', '');
        $param['name']                = Request::param('name/s', '');
        $param['title']               = Request::param('title/s', '');
        $param['keywords']            = Request::param('keywords/s', '');
        $param['description']         = Request::param('description/s', '');
        $param['imgs']                = Request::param('imgs/a', []);
        $param['content']             = Request::param('content/s', '');
        $param['files']               = Request::param('files/a', []);
        $param['sort']                = Request::param('sort/d', 200);

        validate(ArticleValidate::class)->scene('edit')->check($param);

        $data = ArticleService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['article'] = Request::param('article/a', '');

        validate(ArticleValidate::class)->scene('dele')->check($param);

        $data = ArticleService::dele($param['article']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章上传文件")
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
            validate(ArticleValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(ArticleValidate::class)->scene('video')->check($param);
        } else {
            validate(ArticleValidate::class)->scene('file')->check($param);
        }

        $data = ArticleService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("文章是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['article'] = Request::param('article/a', '');
        $param['is_top']  = Request::param('is_top/d', 0);

        validate(ArticleValidate::class)->scene('istop')->check($param);

        $data = ArticleService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['article'] = Request::param('article/a', '');
        $param['is_hot']  = Request::param('is_hot/d', 0);

        validate(ArticleValidate::class)->scene('ishot')->check($param);

        $data = ArticleService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['article'] = Request::param('article/a', '');
        $param['is_rec']  = Request::param('is_rec/d', 0);

        validate(ArticleValidate::class)->scene('isrec')->check($param);

        $data = ArticleService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['article'] = Request::param('article/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(ArticleValidate::class)->scene('ishide')->check($param);

        $data = ArticleService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\ArticleCategoryModel\category_name")
     *      )
     * )
     */
    public function recover()
    {
        $page                = Request::param('page/d', 1);
        $limit               = Request::param('limit/d', 10);
        $sort_field          = Request::param('sort_field/s ', '');
        $sort_type           = Request::param('sort_type/s', '');
        $article_id          = Request::param('article_id/d', '');
        $name                = Request::param('name/s', '');
        $article_category_id = Request::param('article_category_id/d', '');
        $date_type           = Request::param('date_type/s', '');
        $date_range          = Request::param('date_range/a', []);

        validate(ArticleValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($article_id) {
            $where[] = ['article_id', '=', $article_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($article_category_id !== '') {
            $where[] = ['article_category_id', '=', $article_category_id];
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

        $data = ArticleService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['article'] = Request::param('article/a', '');

        validate(ArticleValidate::class)->scene('dele')->check($param);

        $data = ArticleService::recoverReco($param['article']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("article", type="array", require=true, desc="文章列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['article'] = Request::param('article/a', '');

        validate(ArticleValidate::class)->scene('dele')->check($param);

        $data = ArticleService::recoverDele($param['article']);

        return success($data);
    }
}
