<?php
/*
 * @Description  : 新闻管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\NewsValidate;
use app\common\service\NewsService;
use app\common\service\NewsCategoryService;

/**
 * @Apidoc\Title("新闻管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class News
{
    /**
     * @Apidoc\Title("新闻分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = NewsCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProductModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\category_name")
     *      )
     * )
     */
    public function list()
    {
        $page             = Request::param('page/d', 1);
        $limit            = Request::param('limit/d', 10);
        $sort_field       = Request::param('sort_field/s ', '');
        $sort_type        = Request::param('sort_type/s', '');
        $news_id          = Request::param('news_id/d', '');
        $name             = Request::param('name/s', '');
        $news_category_id = Request::param('news_category_id/d', '');
        $date_type        = Request::param('date_type/s', '');
        $date_range       = Request::param('date_range/a', []);

        validate(NewsValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($news_id) {
            $where[] = ['news_id', '=', $news_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($news_category_id) {
            $where[] = ['news_category_id', '=', $news_category_id];
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

        $data = NewsService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\NewsModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\NewsModel\imgfile")
     * )
     */
    public function info()
    {
        $param['news_id'] = Request::param('news_id/d', '');

        validate(NewsValidate::class)->scene('info')->check($param);

        $data = NewsService::info($param['news_id']);
        if ($data['is_delete'] == 1) {
            exception('新闻已被删除：' . $param['news_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\add")
     * @Apidoc\Param(ref="app\common\model\NewsModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['news_category_id'] = Request::param('news_category_id/d', '');
        $param['name']             = Request::param('name/s', '');
        $param['title']            = Request::param('title/s', '');
        $param['keywords']         = Request::param('keywords/s', '');
        $param['description']      = Request::param('description/s', '');
        $param['imgs']             = Request::param('imgs/a', []);
        $param['content']          = Request::param('content/s', '');
        $param['files']            = Request::param('files/a', []);
        $param['sort']             = Request::param('sort/d', 200);

        validate(NewsValidate::class)->scene('add')->check($param);

        $data = NewsService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\edit")
     * @Apidoc\Param(ref="app\common\model\NewsModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['news_id']          = Request::param('news_id/d', '');
        $param['news_category_id'] = Request::param('news_category_id/d', '');
        $param['name']             = Request::param('name/s', '');
        $param['title']            = Request::param('title/s', '');
        $param['keywords']         = Request::param('keywords/s', '');
        $param['description']      = Request::param('description/s', '');
        $param['imgs']             = Request::param('imgs/a', []);
        $param['content']          = Request::param('content/s', '');
        $param['files']            = Request::param('files/a', []);
        $param['sort']             = Request::param('sort/d', 200);

        validate(NewsValidate::class)->scene('edit')->check($param);

        $data = NewsService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['news'] = Request::param('news/a', '');

        validate(NewsValidate::class)->scene('dele')->check($param);

        $data = NewsService::dele($param['news']);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻上传文件")
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
            validate(NewsValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(NewsValidate::class)->scene('video')->check($param);
        } else {
            validate(NewsValidate::class)->scene('file')->check($param);
        }

        $data = NewsService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("新闻是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Param(ref="app\common\model\NewsModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['news']   = Request::param('news/a', '');
        $param['is_top'] = Request::param('is_top/d', 0);

        validate(NewsValidate::class)->scene('istop')->check($param);

        $data = NewsService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Param(ref="app\common\model\NewsModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['news']   = Request::param('news/a', '');
        $param['is_hot'] = Request::param('is_hot/d', 0);

        validate(NewsValidate::class)->scene('ishot')->check($param);

        $data = NewsService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Param(ref="app\common\model\NewsModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['news']   = Request::param('news/a', '');
        $param['is_rec'] = Request::param('is_rec/d', 0);

        validate(NewsValidate::class)->scene('isrec')->check($param);

        $data = NewsService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Param(ref="app\common\model\NewsModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['news']    = Request::param('news/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(NewsValidate::class)->scene('ishide')->check($param);

        $data = NewsService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ProductModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\category_name")
     *      )
     * )
     */
    public function recover()
    {
        $page             = Request::param('page/d', 1);
        $limit            = Request::param('limit/d', 10);
        $sort_field       = Request::param('sort_field/s ', '');
        $sort_type        = Request::param('sort_type/s', '');
        $news_id          = Request::param('news_id/d', '');
        $name             = Request::param('name/s', '');
        $news_category_id = Request::param('news_category_id/d', '');
        $date_type        = Request::param('date_type/s', '');
        $date_range       = Request::param('date_range/a', []);

        validate(NewsValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($news_id) {
            $where[] = ['news_id', '=', $news_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($news_category_id !== '') {
            $where[] = ['news_category_id', '=', $news_category_id];
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

        $data = NewsService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['news'] = Request::param('news/a', '');

        validate(NewsValidate::class)->scene('dele')->check($param);

        $data = NewsService::recoverReco($param['news']);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("news", type="array", require=true, desc="新闻列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['news'] = Request::param('news/a', '');

        validate(NewsValidate::class)->scene('dele')->check($param);

        $data = NewsService::recoverDele($param['news']);

        return success($data);
    }
}
