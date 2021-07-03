<?php
/*
 * @Description  : 友链管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-01
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\LinksValidate;
use app\common\service\LinksService;

/**
 * @Apidoc\Title("友链管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Links
{
    /**
     * @Apidoc\Title("友链列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\LinksModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\LinksModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $links_id   = Request::param('links_id/d', '');
        $name       = Request::param('name/s', '');
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

        validate(LinksValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($links_id) {
            $where[] = ['links_id', '=', $links_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
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

        $data = LinksService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\LinksModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\LinksModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\LinksModel\imgs")
     * )
     */
    public function info()
    {
        $param['links_id'] = Request::param('links_id/d', '');

        validate(LinksValidate::class)->scene('info')->check($param);

        $data = LinksService::info($param['links_id']);
        if ($data['is_delete'] == 1) {
            exception('友链已被删除：' . $param['links_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("友链添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\LinksModel\add")
     * @Apidoc\Param(ref="app\common\model\LinksModel\imgs")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['name'] = Request::param('name/s', '');
        $param['url']  = Request::param('url/s', '');
        $param['imgs'] = Request::param('imgs/a', []);
        $param['sort'] = Request::param('sort/d', 200);

        validate(LinksValidate::class)->scene('add')->check($param);

        $data = LinksService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\LinksModel\edit")
     * @Apidoc\Param(ref="app\common\model\LinksModel\imgs")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['links_id'] = Request::param('links_id/d', '');
        $param['name']     = Request::param('name/s', '');
        $param['url']      = Request::param('url/s', '');
        $param['imgs']     = Request::param('imgs/a', []);
        $param['sort']     = Request::param('sort/d', 200);

        validate(LinksValidate::class)->scene('edit')->check($param);

        $data = LinksService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['links'] = Request::param('links/a', '');

        validate(LinksValidate::class)->scene('dele')->check($param);

        $data = LinksService::dele($param['links']);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链上传文件")
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
            validate(LinksValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(LinksValidate::class)->scene('video')->check($param);
        } else {
            validate(LinksValidate::class)->scene('file')->check($param);
        }

        $data = LinksService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("友链是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Param(ref="app\common\model\LinksModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['links']  = Request::param('links/a', '');
        $param['is_top'] = Request::param('is_top/d', 0);

        validate(LinksValidate::class)->scene('istop')->check($param);

        $data = LinksService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Param(ref="app\common\model\LinksModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['links']  = Request::param('links/a', '');
        $param['is_hot'] = Request::param('is_hot/d', 0);

        validate(LinksValidate::class)->scene('ishot')->check($param);

        $data = LinksService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Param(ref="app\common\model\LinksModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['links']  = Request::param('links/a', '');
        $param['is_rec'] = Request::param('is_rec/d', 0);

        validate(LinksValidate::class)->scene('isrec')->check($param);

        $data = LinksService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Param(ref="app\common\model\LinksModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['links']   = Request::param('links/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(LinksValidate::class)->scene('ishide')->check($param);

        $data = LinksService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\LinksModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\LinksModel\list")
     *      )
     * )
     */
    public function recover()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $links_id   = Request::param('links_id/d', '');
        $name       = Request::param('name/s', '');
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

        validate(LinksValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($links_id) {
            $where[] = ['links_id', '=', $links_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
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

        $data = LinksService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['links'] = Request::param('links/a', '');

        validate(LinksValidate::class)->scene('dele')->check($param);

        $data = LinksService::recoverReco($param['links']);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("links", type="array", require=true, desc="友链列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['links'] = Request::param('links/a', '');

        validate(LinksValidate::class)->scene('dele')->check($param);

        $data = LinksService::recoverDele($param['links']);

        return success($data);
    }
}
