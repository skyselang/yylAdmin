<?php
/*
 * @Description  : 新闻管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-09
 * @LastEditTime : 2021-05-25
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\NewsValidate;
use app\common\service\NewsService;
use app\common\service\NewsCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("新闻管理")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("30")
 */
class News
{
    /**
     * @Apidoc\Title("新闻列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("news_id", type="int", default="", desc="ID")
     * @Apidoc\Param("title", type="string", default="", desc="标题")
     * @Apidoc\Param("date_type", type="string", default="", desc="时间类型")
     * @Apidoc\Param("date_range", type="array", default="", desc="日期范围")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\list")
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
        $title            = Request::param('title/s', '');
        $news_category_id = Request::param('news_category_id/d', '');
        $date_type        = Request::param('date_type/s', '');
        $date_range       = Request::param('date_range/a', []);

        $where = [];
        if ($news_id) {
            $where[] = ['news_id', '=', $news_id];
        }
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
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
        }

        $data = NewsService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = NewsCategoryService::list([], 1, 9999, []);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\NewsModel\info")
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
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['news_category_id'] = Request::param('news_category_id/d', 0);
        $param['img']              = Request::param('img/s', '');
        $param['title']            = Request::param('title/s', '');
        $param['intro']            = Request::param('intro/s', '');
        $param['author']           = Request::param('author/s', '');
        $param['time']             = Request::param('time/s', '');
        $param['source']           = Request::param('source/s', '');
        $param['source_url']       = Request::param('source_url/s', '');
        $param['sort']             = Request::param('sort/d', 200);
        $param['content']          = Request::param('content/s', '');

        validate(NewsValidate::class)->scene('add')->check($param);

        $data = NewsService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['news_id']          = Request::param('news_id/d', '');
        $param['news_category_id'] = Request::param('news_category_id/d', 0);
        $param['img']              = Request::param('img/s', '');
        $param['title']            = Request::param('title/s', '');
        $param['intro']            = Request::param('intro/s', '');
        $param['author']           = Request::param('author/s', '');
        $param['time']             = Request::param('time/s', '');
        $param['source']           = Request::param('source/s', '');
        $param['source_url']       = Request::param('source_url/s', '');
        $param['sort']             = Request::param('sort/d', 200);
        $param['content']          = Request::param('content/s', '');

        validate(NewsValidate::class)->scene('edit')->check($param);

        $data = NewsService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['news_id'] = Request::param('news_id/d', '');

        validate(NewsValidate::class)->scene('dele')->check($param);

        $data = NewsService::dele($param['news_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻上传文件")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("type", type="string", require=true, default="", desc="image、file")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="文件")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("type", type="string", desc="类型"),
     *      @Apidoc\Returned("file_path", type="string", desc="文件路径"),
     *      @Apidoc\Returned("file_url", type="string", desc="文件链接"),
     * )
     */
    public function upload()
    {
        $param['type'] = Request::param('type/s', 'image');
        $param['file'] = Request::file('file');

        if ($param['type'] == 'image') {
            $param['image'] = $param['file'];

            validate(NewsValidate::class)->scene('image')->check($param);
        } else {
            validate(NewsValidate::class)->scene('file')->check($param);
        }

        $data = NewsService::upload($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['news_id'] = Request::param('news_id/d', '');
        $param['is_top']  = Request::param('is_top/d', 0);

        validate(NewsValidate::class)->scene('istop')->check($param);

        $data = NewsService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['news_id'] = Request::param('news_id/d', '');
        $param['is_hot']  = Request::param('is_hot/d', 0);

        validate(NewsValidate::class)->scene('ishot')->check($param);

        $data = NewsService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['news_id'] = Request::param('news_id/d', '');
        $param['is_rec']  = Request::param('is_rec/d', 0);

        validate(NewsValidate::class)->scene('isrec')->check($param);

        $data = NewsService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\NewsModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['news_id'] = Request::param('news_id/d', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(NewsValidate::class)->scene('ishide')->check($param);

        $data = NewsService::ishide($param);

        return success($data);
    }
}
