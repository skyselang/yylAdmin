<?php
/*
 * @Description  : 新闻
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\NewsValidate;
use app\common\service\NewsService;
use app\common\service\NewsCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("新闻")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class News
{
    /**
     * @Apidoc\Title("分类列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\NewsCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = [];
        $list = NewsCategoryService::list('list');
        foreach ($list as $k => $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = NewsCategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\NewsModel\indexList")
     * @Apidoc\Returned(ref="returnCode"),
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
        $name             = Request::param('name/s', '');
        $news_category_id = Request::param('news_category_id/d', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($news_category_id) {
            $where[] = ['news_category_id', '=', $news_category_id];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = NewsService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("新闻信息")
     * @Apidoc\Param(ref="app\common\model\NewsModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\NewsModel\info"),
     *      @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\name")
     *      ),
     *      @Apidoc\Returned("next_info", type="object", desc="下一条",
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\NewsModel\name")
     *      )
     * )
     */
    public function info()
    {
        $param['news_id'] = Request::param('news_id/d', '');

        validate(NewsValidate::class)->scene('info')->check($param);

        $data = NewsService::info($param['news_id']);

        if ($data['is_delete'] == 1) {
            exception('新闻已被删除');
        }

        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = NewsService::prev($data['news_id']);
        $data['next_info'] = NewsService::next($data['news_id']);

        return success($data);
    }
}
