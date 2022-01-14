<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容控制器
namespace app\index\controller\cms;

use think\facade\Request;
use app\common\validate\cms\ContentValidate;
use app\common\service\cms\CategoryService;
use app\common\service\cms\ContentService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容")
 * @Apidoc\Sort("610")
 * @Apidoc\Group("cms")
 */
class Content
{
    /**
     * @Apidoc\Title("内容分类")
     * @Apidoc\Returned("list", type="array", desc="分类树形", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\listReturn")
     * )
     */
    public function category()
    {
        $data = [];
        $list = CategoryService::list('list');
        foreach ($list as $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = CategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\name")
     * @Apidoc\Param("name", require=false)
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\category_id")
     * @Apidoc\Param("category_id", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="内容列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\ContentModel\listReturn"),
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\category_name")
     * )
     */
    public function list()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $sort_field  = Request::param('sort_field/s', '');
        $sort_value  = Request::param('sort_value/s', '');
        $category_id = Request::param('category_id/d', '');
        $name        = Request::param('name/s', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($category_id) {
            $where[] = ['category_id', '=', $category_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        } else {
            $order = ['is_top' => 'desc', 'is_hot' => 'desc', 'is_rec' => 'desc', 'sort' => 'desc', 'create_time' => 'desc'];
        }

        $data = ContentService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\id")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\category_id", require=false)
     * @Apidoc\Returned(ref="app\common\model\cms\ContentModel\infoReturn")
     * @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\category_name")
     * @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *     @Apidoc\Returned(ref="app\common\model\cms\ContentModel\id"),
     *     @Apidoc\Returned(ref="app\common\model\cms\ContentModel\name")
     * )
     * @Apidoc\Returned("next_info", type="object", desc="下一条",
     *     @Apidoc\Returned(ref="app\common\model\cms\ContentModel\id"),
     *     @Apidoc\Returned(ref="app\common\model\cms\ContentModel\name")
     * )
     */
    public function info()
    {
        $param['content_id']  = Request::param('content_id/d', '');
        $param['category_id'] = Request::param('category_id/d', 0);

        validate(ContentValidate::class)->scene('info')->check($param);

        $data = ContentService::info($param['content_id']);
        if ($data['is_delete'] == 1) {
            exception('内容已被删除');
        }
        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = ContentService::prev($data['content_id'], $param['category_id']);
        $data['next_info'] = ContentService::next($data['content_id'], $param['category_id']);

        return success($data);
    }
}
