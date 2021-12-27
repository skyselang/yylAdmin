<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容管理控制器
namespace app\admin\controller\cms;

use think\facade\Request;
use app\common\validate\cms\ContentValidate;
use app\common\service\cms\ContentService;
use app\common\service\cms\CategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("310")
 */
class Content
{
    /**
     * @Apidoc\Title("内容分类")
     * @Apidoc\Returned("list", type="array", desc="树形列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\listReturn")
     * )
     */
    public function category()
    {
        $data['list'] = CategoryService::list('tree');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn"),
     * @Apidoc\Returned("list", type="array", desc="内容列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\ContentModel\listReturn"),
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\category_name")
     * )
     */
    public function list()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');
        $category_id  = Request::param('category_id/d', '');

        validate(ContentValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_value' => $sort_value]);

        if ($category_id) {
            $where[] = ['category_id', '=', $category_id];
        }
        if ($search_field && $search_value) {
            if ($search_field == 'content_id') {
                $exp = strstr($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $exp, $search_value];
            } elseif (in_array($search_field, ['is_top', 'is_hot', 'is_rec', 'is_hide'])) {
                if ($search_value == '是' || $search_value == '1') {
                    $search_value = 1;
                } else {
                    $search_value = 0;
                }
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $where[] = ['is_delete', '=', 0];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = ContentService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\id")
     * @Apidoc\Returned(ref="app\common\model\cms\ContentModel\infoReturn")
     * @Apidoc\Returned(ref="app\common\model\cms\ContentModel\imgs")
     * @Apidoc\Returned(ref="app\common\model\cms\ContentModel\files")
     * @Apidoc\Returned(ref="app\common\model\cms\ContentModel\videos")
     */
    public function info()
    {
        $param['content_id'] = Request::param('content_id/d', '');

        validate(ContentValidate::class)->scene('info')->check($param);

        $data = ContentService::info($param['content_id']);
        if ($data['is_delete'] == 1) {
            exception('内容已被删除：' . $param['content_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("内容添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\addParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\imgs")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\files")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\videos")
     */
    public function add()
    {
        $param['category_id'] = Request::param('category_id/d', 0);
        $param['name']        = Request::param('name/s', '');
        $param['title']       = Request::param('title/s', '');
        $param['keywords']    = Request::param('keywords/s', '');
        $param['description'] = Request::param('description/s', '');
        $param['imgs']        = Request::param('imgs/a', []);
        $param['files']       = Request::param('files/a', []);
        $param['videos']      = Request::param('videos/a', []);
        $param['url']         = Request::param('url/s', '');
        $param['sort']        = Request::param('sort/d', 250);
        $param['content']     = Request::param('content/s', '');

        validate(ContentValidate::class)->scene('add')->check($param);

        $data = ContentService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\editParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\imgs")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\files")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\videos")
     */
    public function edit()
    {
        $param['content_id']  = Request::param('content_id/d', '');
        $param['category_id'] = Request::param('category_id/d', 0);
        $param['name']        = Request::param('name/s', '');
        $param['title']       = Request::param('title/s', '');
        $param['keywords']    = Request::param('keywords/s', '');
        $param['description'] = Request::param('description/s', '');
        $param['imgs']        = Request::param('imgs/a', []);
        $param['files']       = Request::param('files/a', []);
        $param['videos']      = Request::param('videos/a', []);
        $param['url']         = Request::param('url/s', '');
        $param['sort']        = Request::param('sort/d', 250);
        $param['content']     = Request::param('content/s', '');

        validate(ContentValidate::class)->scene('edit')->check($param);

        $data = ContentService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(ContentValidate::class)->scene('dele')->check($param);

        $data = ContentService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置分类")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\category_id")
     */
    public function cate()
    {
        $param['ids']         = Request::param('ids/a', '');
        $param['category_id'] = Request::param('category_id/d', 0);

        validate(ContentValidate::class)->scene('cate')->check($param);

        $data = ContentService::cate($param['ids'], $param['category_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_top")
     */
    public function istop()
    {
        $param['ids']    = Request::param('ids/a', '');
        $param['is_top'] = Request::param('is_top/d', 0);

        validate(ContentValidate::class)->scene('istop')->check($param);

        $data = ContentService::istop($param['ids'], $param['is_top']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_hot")
     */
    public function ishot()
    {
        $param['ids']    = Request::param('ids/a', '');
        $param['is_hot'] = Request::param('is_hot/d', 0);

        validate(ContentValidate::class)->scene('ishot')->check($param);

        $data = ContentService::ishot($param['ids'], $param['is_hot']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_rec")
     */
    public function isrec()
    {
        $param['ids']    = Request::param('ids/a', '');
        $param['is_rec'] = Request::param('is_rec/d', 0);

        validate(ContentValidate::class)->scene('isrec')->check($param);

        $data = ContentService::isrec($param['ids'], $param['is_rec']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_hide")
     */
    public function ishide()
    {
        $param['ids']     = Request::param('ids/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(ContentValidate::class)->scene('ishide')->check($param);

        $data = ContentService::ishide($param['ids'], $param['is_hide']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="内容列表", 
     *    @Apidoc\Returned(ref="app\common\model\cms\ContentModel\listReturn"),
     *    @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\category_name"),
     * )
     */
    public function recover()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');
        $category_id  = Request::param('category_id/d', '');

        validate(ContentValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_value' => $sort_value]);

        if ($category_id) {
            $where[] = ['category_id', '=', $category_id];
        }
        if ($search_field && $search_value) {
            if ($search_field == 'content_id') {
                $where[] = [$search_field, '=', $search_value];
            } elseif (in_array($search_field, ['is_top', 'is_hot', 'is_rec', 'is_hide'])) {
                if ($search_value == '是' || $search_value == '1') {
                    $search_value = 1;
                } else {
                    $search_value = 0;
                }
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        $where[] = ['is_delete', '=', 1];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        } else {
            $order = ['delete_time' => 'desc', 'content_id' => 'desc'];
        }

        $data = ContentService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     */
    public function recoverReco()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(ContentValidate::class)->scene('reco')->check($param);

        $data = ContentService::recoverReco($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\content")
     */
    public function recoverDele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(ContentValidate::class)->scene('dele')->check($param);

        $data = ContentService::recoverDele($param['ids']);

        return success($data);
    }
}
