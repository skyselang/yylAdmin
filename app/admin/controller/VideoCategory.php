<?php
/*
 * @Description  : 视频分类控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-30
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\VideoCategoryValidate;
use app\common\service\VideoCategoryService;

/**
 * @Apidoc\Title("视频分类")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class VideoCategory
{
    /**
     * @Apidoc\Title("视频分类列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\VideoCategoryModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = VideoCategoryService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("视频分类信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\VideoCategoryModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\VideoCategoryModel\info")
     * )
     */
    public function info()
    {
        $param['video_category_id'] = Request::param('video_category_id/d', '');

        validate(VideoCategoryValidate::class)->scene('info')->check($param);

        $data = VideoCategoryService::info($param['video_category_id']);
        if ($data['is_delete'] == 1) {
            exception('视频分类已被删除：' . $param['video_category_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("视频分类添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\VideoCategoryModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['video_category_pid'] = Request::param('video_category_pid/d', 0);
        $param['category_name']      = Request::param('category_name/s', '');
        $param['title']              = Request::param('title/s', '');
        $param['keywords']           = Request::param('keywords/s', '');
        $param['description']        = Request::param('description/s', '');
        $param['imgs']               = Request::param('imgs/a', []);
        $param['sort']               = Request::param('sort/d', 200);

        validate(VideoCategoryValidate::class)->scene('add')->check($param);

        $data = VideoCategoryService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频分类修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\VideoCategoryModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['video_category_id']  = Request::param('video_category_id/d', '');
        $param['video_category_pid'] = Request::param('video_category_pid/d', 0);
        $param['category_name']      = Request::param('category_name/s', '');
        $param['title']              = Request::param('title/s', '');
        $param['keywords']           = Request::param('keywords/s', '');
        $param['description']        = Request::param('description/s', '');
        $param['imgs']               = Request::param('imgs/a', []);
        $param['sort']               = Request::param('sort/d', 200);

        validate(VideoCategoryValidate::class)->scene('edit')->check($param);

        $data = VideoCategoryService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频分类删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video_category", type="array", require=true, desc="视频分类列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['video_category'] = Request::param('video_category/a', '');

        validate(VideoCategoryValidate::class)->scene('dele')->check($param);

        $data = VideoCategoryService::dele($param['video_category']);

        return success($data);
    }

    /**
     * @Apidoc\Title("视频分类上传图片")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="图片")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("path", type="string", desc="文件路径"),
     *      @Apidoc\Returned("url", type="string", desc="文件链接"),
     * )
     */
    public function upload()
    {
        $param['type'] = Request::param('type/s', 'image');
        $param['file'] = Request::file('file');

        $param[$param['type']] = $param['file'];
        if ($param['type'] == 'image') {
            validate(VideoCategoryValidate::class)->scene('image')->check($param);
        } elseif ($param['type'] == 'video') {
            validate(VideoCategoryValidate::class)->scene('video')->check($param);
        } else {
            validate(VideoCategoryValidate::class)->scene('file')->check($param);
        }

        $data = VideoCategoryService::upload($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("视频分类是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("video_category", type="array", require=true, desc="视频分类列表")
     * @Apidoc\Param(ref="app\common\model\VideoCategoryModel\ishide")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function ishide()
    {
        $param['video_category'] = Request::param('video_category/a', '');
        $param['is_hide']        = Request::param('is_hide/d', 0);

        validate(VideoCategoryValidate::class)->scene('ishide')->check($param);

        $data = VideoCategoryService::ishide($param);

        return success($data);
    }
}
