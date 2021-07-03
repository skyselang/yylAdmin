<?php
/*
 * @Description  : 下载管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\DownloadValidate;
use app\common\service\DownloadService;
use app\common\service\DownloadCategoryService;

/**
 * @Apidoc\Title("下载管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Download
{
    /**
     * @Apidoc\Title("下载分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\DownloadCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = DownloadCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("下载列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\DownloadCategoryModel\category_name")
     *      )
     * )
     */
    public function list()
    {
        $page                 = Request::param('page/d', 1);
        $limit                = Request::param('limit/d', 10);
        $sort_field           = Request::param('sort_field/s ', '');
        $sort_type            = Request::param('sort_type/s', '');
        $download_id          = Request::param('download_id/d', '');
        $name                 = Request::param('name/s', '');
        $download_category_id = Request::param('download_category_id/d', '');
        $date_type            = Request::param('date_type/s', '');
        $date_range           = Request::param('date_range/a', []);

        validate(DownloadValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($download_id) {
            $where[] = ['download_id', '=', $download_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($download_category_id) {
            $where[] = ['download_category_id', '=', $download_category_id];
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

        $data = DownloadService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\DownloadModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\DownloadModel\imgfile")
     * )
     */
    public function info()
    {
        $param['download_id'] = Request::param('download_id/d', '');

        validate(DownloadValidate::class)->scene('info')->check($param);

        $data = DownloadService::info($param['download_id']);
        if ($data['is_delete'] == 1) {
            exception('下载已被删除：' . $param['download_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("下载添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\add")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['download_category_id'] = Request::param('download_category_id/d', '');
        $param['name']                 = Request::param('name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['content']              = Request::param('content/s', '');
        $param['files']                = Request::param('files/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(DownloadValidate::class)->scene('add')->check($param);

        $data = DownloadService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\edit")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['download_id']          = Request::param('download_id/d', '');
        $param['download_category_id'] = Request::param('download_category_id/d', '');
        $param['name']                 = Request::param('name/s', '');
        $param['title']                = Request::param('title/s', '');
        $param['keywords']             = Request::param('keywords/s', '');
        $param['description']          = Request::param('description/s', '');
        $param['imgs']                 = Request::param('imgs/a', []);
        $param['content']              = Request::param('content/s', '');
        $param['files']                = Request::param('files/a', []);
        $param['sort']                 = Request::param('sort/d', 200);

        validate(DownloadValidate::class)->scene('edit')->check($param);

        $data = DownloadService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['download'] = Request::param('download/a', '');

        validate(DownloadValidate::class)->scene('dele')->check($param);

        $data = DownloadService::dele($param['download']);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载上传文件")
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
            validate(DownloadValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(DownloadValidate::class)->scene('video')->check($param);
        } else {
            validate(DownloadValidate::class)->scene('file')->check($param);
        }

        $data = DownloadService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("下载是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['download'] = Request::param('download/a', '');
        $param['is_top']   = Request::param('is_top/d', 0);

        validate(DownloadValidate::class)->scene('istop')->check($param);

        $data = DownloadService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['download'] = Request::param('download/a', '');
        $param['is_hot']   = Request::param('is_hot/d', 0);

        validate(DownloadValidate::class)->scene('ishot')->check($param);

        $data = DownloadService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['download'] = Request::param('download/a', '');
        $param['is_rec']   = Request::param('is_rec/d', 0);

        validate(DownloadValidate::class)->scene('isrec')->check($param);

        $data = DownloadService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['download'] = Request::param('download/a', '');
        $param['is_hide']  = Request::param('is_hide/d', 0);

        validate(DownloadValidate::class)->scene('ishide')->check($param);

        $data = DownloadService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\DownloadCategoryModel\category_name")
     *      )
     * )
     */
    public function recover()
    {
        $page                 = Request::param('page/d', 1);
        $limit                = Request::param('limit/d', 10);
        $sort_field           = Request::param('sort_field/s ', '');
        $sort_type            = Request::param('sort_type/s', '');
        $download_id          = Request::param('download_id/d', '');
        $name                 = Request::param('name/s', '');
        $download_category_id = Request::param('download_category_id/d', '');
        $date_type            = Request::param('date_type/s', '');
        $date_range           = Request::param('date_range/a', []);

        validate(DownloadValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($download_id) {
            $where[] = ['download_id', '=', $download_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($download_category_id !== '') {
            $where[] = ['download_category_id', '=', $download_category_id];
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

        $data = DownloadService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['download'] = Request::param('download/a', '');

        validate(DownloadValidate::class)->scene('dele')->check($param);

        $data = DownloadService::recoverReco($param['download']);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("download", type="array", require=true, desc="下载列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['download'] = Request::param('download/a', '');

        validate(DownloadValidate::class)->scene('dele')->check($param);

        $data = DownloadService::recoverDele($param['download']);

        return success($data);
    }
}
