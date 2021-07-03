<?php
/*
 * @Description  : 轮播管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-02
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\CarouselValidate;
use app\common\service\CarouselService;

/**
 * @Apidoc\Title("轮播管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Carousel
{
    /**
     * @Apidoc\Title("轮播列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CarouselModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $carousel_id = Request::param('carousel_id/d', '');
        $name        = Request::param('name/s', '');
        $date_type   = Request::param('date_type/s', '');
        $date_range  = Request::param('date_range/a', []);

        validate(CarouselValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($carousel_id) {
            $where[] = ['carousel_id', '=', $carousel_id];
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

        $data = CarouselService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\CarouselModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\CarouselModel\imgs")
     * )
     */
    public function info()
    {
        $param['carousel_id'] = Request::param('carousel_id/d', '');

        validate(CarouselValidate::class)->scene('info')->check($param);

        $data = CarouselService::info($param['carousel_id']);
        if ($data['is_delete'] == 1) {
            exception('轮播已被删除：' . $param['carousel_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\add")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\imgs")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['name'] = Request::param('name/s', '');
        $param['url']  = Request::param('url/s', '');
        $param['imgs'] = Request::param('imgs/a', []);
        $param['sort'] = Request::param('sort/d', 200);

        validate(CarouselValidate::class)->scene('add')->check($param);

        $data = CarouselService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\edit")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\imgs")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['carousel_id'] = Request::param('carousel_id/d', '');
        $param['name']        = Request::param('name/s', '');
        $param['url']         = Request::param('url/s', '');
        $param['imgs']        = Request::param('imgs/a', []);
        $param['sort']        = Request::param('sort/d', 200);

        validate(CarouselValidate::class)->scene('edit')->check($param);

        $data = CarouselService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['carousel'] = Request::param('carousel/a', '');

        validate(CarouselValidate::class)->scene('dele')->check($param);

        $data = CarouselService::dele($param['carousel']);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播上传文件")
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
            validate(CarouselValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(CarouselValidate::class)->scene('video')->check($param);
        } else {
            validate(CarouselValidate::class)->scene('file')->check($param);
        }

        $data = CarouselService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("轮播是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['carousel']  = Request::param('carousel/a', '');
        $param['is_top'] = Request::param('is_top/d', 0);

        validate(CarouselValidate::class)->scene('istop')->check($param);

        $data = CarouselService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['carousel'] = Request::param('carousel/a', '');
        $param['is_hot']   = Request::param('is_hot/d', 0);

        validate(CarouselValidate::class)->scene('ishot')->check($param);

        $data = CarouselService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['carousel'] = Request::param('carousel/a', '');
        $param['is_rec']   = Request::param('is_rec/d', 0);

        validate(CarouselValidate::class)->scene('isrec')->check($param);

        $data = CarouselService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['carousel'] = Request::param('carousel/a', '');
        $param['is_hide']  = Request::param('is_hide/d', 0);

        validate(CarouselValidate::class)->scene('ishide')->check($param);

        $data = CarouselService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CarouselModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CarouselModel\list")
     *      )
     * )
     */
    public function recover()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $carousel_id = Request::param('carousel_id/d', '');
        $name        = Request::param('name/s', '');
        $date_type   = Request::param('date_type/s', '');
        $date_range  = Request::param('date_range/a', []);

        validate(CarouselValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($carousel_id) {
            $where[] = ['carousel_id', '=', $carousel_id];
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

        $data = CarouselService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['carousel'] = Request::param('carousel/a', '');

        validate(CarouselValidate::class)->scene('dele')->check($param);

        $data = CarouselService::recoverReco($param['carousel']);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("carousel", type="array", require=true, desc="轮播列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['carousel'] = Request::param('carousel/a', '');

        validate(CarouselValidate::class)->scene('dele')->check($param);

        $data = CarouselService::recoverDele($param['carousel']);

        return success($data);
    }
}
