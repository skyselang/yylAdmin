<?php
/*
 * @Description  : 内容管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-09
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\CmsValidate;
use app\common\service\CmsService;
use app\common\service\CmsCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Cms
{
    /**
     * @Apidoc\Title("内容分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CmsCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = CmsCategoryService::list('tree');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CmsModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\CmsCategoryModel\name")
     *      )
     * )
     */
    public function list()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $cms_id      = Request::param('cms_id/d', '');
        $name        = Request::param('name/s', '');
        $category_id = Request::param('category_id/d', '');
        $date_type   = Request::param('date_type/s', '');
        $date_range  = Request::param('date_range/a', []);

        validate(CmsValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($cms_id) {
            $where[] = ['cms_id', '=', $cms_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($category_id) {
            $where[] = ['category_id', '=', $category_id];
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

        $data = CmsService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\CmsModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\CmsModel\imgs"),
     *      @Apidoc\Returned(ref="app\common\model\CmsModel\files"),
     *      @Apidoc\Returned(ref="app\common\model\CmsModel\videos"),
     * )
     */
    public function info()
    {
        $param['cms_id'] = Request::param('cms_id/d', '');

        validate(CmsValidate::class)->scene('info')->check($param);

        $data = CmsService::info($param['cms_id']);
        if ($data['is_delete'] == 1) {
            exception('内容已被删除：' . $param['cms_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("内容添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\add")
     * @Apidoc\Param(ref="app\common\model\CmsModel\imgs")
     * @Apidoc\Param(ref="app\common\model\CmsModel\files")
     * @Apidoc\Param(ref="app\common\model\CmsModel\videos")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['category_id'] = Request::param('category_id/d', '');
        $param['name']        = Request::param('name/s', '');
        $param['title']       = Request::param('title/s', '');
        $param['keywords']    = Request::param('keywords/s', '');
        $param['description'] = Request::param('description/s', '');
        $param['content']     = Request::param('content/s', '');
        $param['imgs']        = Request::param('imgs/a', []);
        $param['files']       = Request::param('files/a', []);
        $param['videos']      = Request::param('videos/a', []);
        $param['url']         = Request::param('url/s', '');
        $param['sort']        = Request::param('sort/d', 200);

        validate(CmsValidate::class)->scene('add')->check($param);

        $data = CmsService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\edit")
     * @Apidoc\Param(ref="app\common\model\CmsModel\imgs")
     * @Apidoc\Param(ref="app\common\model\CmsModel\files")
     * @Apidoc\Param(ref="app\common\model\CmsModel\videos")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['cms_id']      = Request::param('cms_id/d', '');
        $param['category_id'] = Request::param('category_id/d', '');
        $param['name']        = Request::param('name/s', '');
        $param['title']       = Request::param('title/s', '');
        $param['keywords']    = Request::param('keywords/s', '');
        $param['description'] = Request::param('description/s', '');
        $param['content']     = Request::param('content/s', '');
        $param['imgs']        = Request::param('imgs/a', []);
        $param['files']       = Request::param('files/a', []);
        $param['videos']      = Request::param('videos/a', []);
        $param['url']         = Request::param('url/s', '');
        $param['sort']        = Request::param('sort/d', 200);

        validate(CmsValidate::class)->scene('edit')->check($param);

        $data = CmsService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['cms'] = Request::param('cms/a', '');

        validate(CmsValidate::class)->scene('dele')->check($param);

        $data = CmsService::dele($param['cms']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容上传文件")
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
            validate(CmsValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(CmsValidate::class)->scene('video')->check($param);
        } else {
            validate(CmsValidate::class)->scene('file')->check($param);
        }

        $data = CmsService::upload($param['file'], $param['type']);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("内容是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Param(ref="app\common\model\CmsModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['cms']    = Request::param('cms/a', '');
        $param['is_top'] = Request::param('is_top/d', 0);

        validate(CmsValidate::class)->scene('istop')->check($param);

        $data = CmsService::istop($param['cms'], $param['is_top']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Param(ref="app\common\model\CmsModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['cms']    = Request::param('cms/a', '');
        $param['is_hot'] = Request::param('is_hot/d', 0);

        validate(CmsValidate::class)->scene('ishot')->check($param);

        $data = CmsService::ishot($param['cms'], $param['is_hot']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Param(ref="app\common\model\CmsModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['cms']    = Request::param('cms/a', '');
        $param['is_rec'] = Request::param('is_rec/d', 0);

        validate(CmsValidate::class)->scene('isrec')->check($param);

        $data = CmsService::isrec($param['cms'], $param['is_rec']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Param(ref="app\common\model\CmsModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['cms']     = Request::param('cms/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(CmsValidate::class)->scene('ishide')->check($param);

        $data = CmsService::ishide($param['cms'], $param['is_hide']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CmsModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CmsModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\CmsCategoryModel\name"),
     *      )
     * )
     */
    public function recover()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $cms_id      = Request::param('cms_id/d', '');
        $name        = Request::param('name/s', '');
        $category_id = Request::param('category_id/d', '');
        $date_type   = Request::param('date_type/s', '');
        $date_range  = Request::param('date_range/a', []);

        validate(CmsValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($cms_id) {
            $where[] = ['cms_id', '=', $cms_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($category_id !== '') {
            $where[] = ['category_id', '=', $category_id];
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

        $data = CmsService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['cms'] = Request::param('cms/a', '');

        validate(CmsValidate::class)->scene('dele')->check($param);

        $data = CmsService::recoverReco($param['cms']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CmsModel\cms")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['cms'] = Request::param('cms/a', '');

        validate(CmsValidate::class)->scene('dele')->check($param);

        $data = CmsService::recoverDele($param['cms']);

        return success($data);
    }
}
