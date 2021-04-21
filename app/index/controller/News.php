<?php
/*
 * @Description  : 新闻
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-04-21
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\service\NewsService;
use app\common\validate\NewsValidate;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("新闻")
 */
class News
{
    /**
     * @Apidoc\Title("新闻列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("title", type="string", default="", desc="标题")
     * @Apidoc\Returned(ref="return"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", ref="app\common\model\NewsModel\list")
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $title      = Request::param('title/s', '');

        $where = [];
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
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
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\NewsModel\infoIndex")
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

        $data['last_news'] = NewsService::last($data['news_id']);
        $data['next_news'] = NewsService::next($data['news_id']);

        return success($data);
    }
}
