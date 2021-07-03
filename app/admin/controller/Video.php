<?php
/*
 * @Description  : 视频管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\VideoValidate;
use app\common\service\VideoService;
use app\common\service\VideoCategoryService;

/**
 * @Apidoc\Title("视频管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Video
{
    /**
     * @Apidoc\Title("视频分类")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\VideoCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data['list'] = VideoCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("视频列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\VideoModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\VideoModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\VideoCategoryModel\category_name")
     *      )
     * )
     */
    public function list()
    {
        $page              = Request::param('page/d', 1);
        $limit             = Request::param('limit/d', 10);
        $sort_field        = Request::param('sort_field/s ', '');
        $sort_type         = Request::param('sort_type/s', '');
        $video_id          = Request::param('video_id/d', '');
        $name              = Request::param('name/s', '');
        $video_category_id = Request::param('video_category_id/d', '');
        $date_type         = Request::param('date_type/s', '');
        $date_range        = Request::param('date_range/a', []);

        validate(VideoValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($video_id) {
            $where[] = ['video_id', '=', $video_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($video_category_id) {
            $where[] = ['video_category_id', '=', $video_category_id];
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

        $data = VideoService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\VideoModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\VideoModel\info"),
     *      @Apidoc\Returned(ref="app\common\model\VideoModel\imgfile")
     * )
     */
    public function info()
    {
        $param['video_id'] = Request::param('video_id/d', '');

        validate(VideoValidate::class)->scene('info')->check($param);

        $data = VideoService::info($param['video_id']);
        if ($data['is_delete'] == 1) {
            exception('视频已被删除：' . $param['video_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("视频添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\VideoModel\add")
     * @Apidoc\Param(ref="app\common\model\VideoModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['video_category_id'] = Request::param('video_category_id/d', '');
        $param['name']              = Request::param('name/s', '');
        $param['title']             = Request::param('title/s', '');
        $param['keywords']          = Request::param('keywords/s', '');
        $param['description']       = Request::param('description/s', '');
        $param['imgs']              = Request::param('imgs/a', []);
        $param['videos']            = Request::param('videos/a', []);
        $param['content']           = Request::param('content/s', '');
        $param['files']             = Request::param('files/a', []);
        $param['sort']              = Request::param('sort/d', 200);

        validate(VideoValidate::class)->scene('add')->check($param);

        $data = VideoService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\VideoModel\edit")
     * @Apidoc\Param(ref="app\common\model\VideoModel\imgfile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['video_id']          = Request::param('video_id/d', '');
        $param['video_category_id'] = Request::param('video_category_id/d', '');
        $param['name']              = Request::param('name/s', '');
        $param['title']             = Request::param('title/s', '');
        $param['keywords']          = Request::param('keywords/s', '');
        $param['description']       = Request::param('description/s', '');
        $param['imgs']              = Request::param('imgs/a', []);
        $param['videos']            = Request::param('videos/a', []);
        $param['content']           = Request::param('content/s', '');
        $param['files']             = Request::param('files/a', []);
        $param['sort']              = Request::param('sort/d', 200);

        validate(VideoValidate::class)->scene('edit')->check($param);

        $data = VideoService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['videos'] = Request::param('video/a', '');

        validate(VideoValidate::class)->scene('dele')->check($param);

        $data = VideoService::dele($param['videos']);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频上传文件")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("type", type="string", require=true, default="file", desc="image、video、file")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="图片、视频、文件")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("type", type="string", desc="类型"),
     *      @Apidoc\Returned("path", type="string", desc="文件路径"),
     *      @Apidoc\Returned("url", type="string", desc="文件链接"),
     * )
     */
    public function upload()
    {
        $param['type'] = Request::param('type/s', 'file');
        $param['file'] = Request::file('file');

        $param[$param['type']] = $param['file'];
        if ($param['type'] == 'image') {
            validate(VideoValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(VideoValidate::class)->scene('video')->check($param);
        } else {
            validate(VideoValidate::class)->scene('file')->check($param);
        }

        $data = VideoService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("视频是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Param(ref="app\common\model\VideoModel\istop")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function istop()
    {
        $param['videos'] = Request::param('video/a', '');
        $param['is_top'] = Request::param('is_top/d', 0);

        validate(VideoValidate::class)->scene('istop')->check($param);

        $data = VideoService::istop($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Param(ref="app\common\model\VideoModel\ishot")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishot()
    {
        $param['videos'] = Request::param('video/a', '');
        $param['is_hot'] = Request::param('is_hot/d', 0);

        validate(VideoValidate::class)->scene('ishot')->check($param);

        $data = VideoService::ishot($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Param(ref="app\common\model\VideoModel\isrec")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isrec()
    {
        $param['videos'] = Request::param('video/a', '');
        $param['is_rec'] = Request::param('is_rec/d', 0);

        validate(VideoValidate::class)->scene('isrec')->check($param);

        $data = VideoService::isrec($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Param(ref="app\common\model\VideoModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['videos']  = Request::param('video/a', '');
        $param['is_hide'] = Request::param('is_hide/d', 0);

        validate(VideoValidate::class)->scene('ishide')->check($param);

        $data = VideoService::ishide($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\VideoModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\VideoModel\list"),
     *          @Apidoc\Returned(ref="app\common\model\VideoCategoryModel\category_name")
     *      )
     * )
     */
    public function recover()
    {
        $page              = Request::param('page/d', 1);
        $limit             = Request::param('limit/d', 10);
        $sort_field        = Request::param('sort_field/s ', '');
        $sort_type         = Request::param('sort_type/s', '');
        $video_id          = Request::param('video_id/d', '');
        $name              = Request::param('name/s', '');
        $video_category_id = Request::param('video_category_id/d', '');
        $date_type         = Request::param('date_type/s', '');
        $date_range        = Request::param('date_range/a', []);

        validate(VideoValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($video_id) {
            $where[] = ['video_id', '=', $video_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($video_category_id !== '') {
            $where[] = ['video_category_id', '=', $video_category_id];
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

        $data = VideoService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['videos'] = Request::param('video/a', '');

        validate(VideoValidate::class)->scene('dele')->check($param);

        $data = VideoService::recoverReco($param['videos']);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video", type="array", require=true, desc="视频列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['videos'] = Request::param('video/a', '');

        validate(VideoValidate::class)->scene('dele')->check($param);

        $data = VideoService::recoverDele($param['videos']);

        return success($data);
    }
}
