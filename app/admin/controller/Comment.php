<?php
/*
 * @Description  : 留言管理控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\CommentValidate;
use app\common\service\CommentService;

/**
 * @Apidoc\Title("留言管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Comment
{
    /**
     * @Apidoc\Title("留言列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CommentModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CommentModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $comment_id = Request::param('comment_id/d', '');
        $keyword    = Request::param('keyword/s', '');
        $is_read    = Request::param('is_read/s', '');
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

        validate(CommentValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 0];
        if ($comment_id) {
            $where[] = ['comment_id', '=', $comment_id];
        }
        if ($keyword) {
            $where[] = ['call|mobile|title', 'like', '%' . $keyword . '%'];
        }
        if ($is_read != '') {
            $where[] = ['is_read', '=', $is_read];
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

        $data = CommentService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CommentModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\CommentModel\info")
     * )
     */
    public function info()
    {
        $param['comment_id'] = Request::param('comment_id/d', '');

        validate(CommentValidate::class)->scene('info')->check($param);

        $data = CommentService::info($param['comment_id']);
        if ($data['is_delete'] == 1) {
            exception('留言已被删除：' . $param['comment_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("留言修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\CommentModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['comment_id'] = Request::param('comment_id/d', '');
        $param['remark']     = Request::param('remark/s', '');

        validate(CommentValidate::class)->scene('edit')->check($param);

        $data = CommentService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("comment", type="array", require=true, desc="留言列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['comment'] = Request::param('comment/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::dele($param['comment']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言已读")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("comment", type="array", require=true, desc="留言列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isread()
    {
        $param['comment'] = Request::param('comment/a', '');

        validate(CommentValidate::class)->scene('isread')->check($param);

        $data = CommentService::isread($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\CommentModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CommentModel\list")
     *      )
     * )
     */
    public function recover()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $comment_id = Request::param('comment_id/d', '');
        $keyword    = Request::param('keyword/s', '');
        $is_read    = Request::param('is_read/s', '');
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

        validate(CommentValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_type' => $sort_type]);

        $where[] = ['is_delete', '=', 1];
        if ($comment_id) {
            $where[] = ['comment_id', '=', $comment_id];
        }
        if ($keyword) {
            $where[] = ['call|mobile|title', 'like', '%' . $keyword . '%'];
        }
        if ($is_read != '') {
            $where[] = ['is_read', '=', $is_read];
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

        $data = CommentService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("comment", type="array", require=true, desc="留言列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverReco()
    {
        $param['comment'] = Request::param('comment/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::recoverReco($param['comment']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("comment", type="array", require=true, desc="留言列表")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function recoverDele()
    {
        $param['comment'] = Request::param('comment/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::recoverDele($param['comment']);

        return success($data);
    }
}
