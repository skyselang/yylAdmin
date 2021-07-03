<?php
/*
 * @Description  : 文章
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\ArticleValidate;
use app\common\service\ArticleService;
use app\common\service\ArticleCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文章")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Article
{
    /**
     * @Apidoc\Title("分类列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ArticleCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = [];
        $list = ArticleCategoryService::list('list');
        foreach ($list as $k => $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = ArticleCategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\indexList")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\list")
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
        $article_category_id = Request::param('article_category_id/d', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($article_category_id) {
            $where[] = ['article_category_id', '=', $article_category_id];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = ArticleService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("文章信息")
     * @Apidoc\Param(ref="app\common\model\ArticleModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ArticleModel\info"),
     *      @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\name")
     *      ),
     *      @Apidoc\Returned("next_info", type="object", desc="下一条",
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\ArticleModel\name")
     *      )
     * )
     */
    public function info()
    {
        $param['article_id'] = Request::param('article_id/d', '');

        validate(ArticleValidate::class)->scene('info')->check($param);

        $data = ArticleService::info($param['article_id']);

        if ($data['is_delete'] == 1) {
            exception('文章已被删除');
        }

        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = ArticleService::prev($data['article_id']);
        $data['next_info'] = ArticleService::next($data['article_id']);

        return success($data);
    }
}
