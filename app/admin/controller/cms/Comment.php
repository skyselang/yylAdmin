<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 留言管理控制器
namespace app\admin\controller\cms;

use think\facade\Request;
use app\common\validate\cms\CommentValidate;
use app\common\service\cms\CommentService;
use hg\apidoc\annotation as Apidoc;

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
     * @Apidoc\Param(ref="paramSort")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\cms\CommentModel\list")
     *      )
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

        validate(CommentValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_value' => $sort_value]);

        $where[] = ['is_delete', '=', 0];
        if ($search_field && $search_value) {
            if ($search_field == 'comment_id') {
                $where[] = [$search_field, '=', $search_value];
            } elseif (in_array($search_field, ['is_read'])) {
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

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $field = '';

        $data = CommentService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\cms\CommentModel\info")
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
     * @Apidoc\Title("留言添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['call']    = Request::param('call/s', '');
        $param['mobile']  = Request::param('mobile/s', '');
        $param['tel']     = Request::param('tel/s', '');
        $param['email']   = Request::param('email/s', '');
        $param['qq']      = Request::param('qq/s', '');
        $param['wechat']  = Request::param('wechat/s', '');
        $param['title']   = Request::param('title/s', '');
        $param['content'] = Request::param('content/s', '');
        $param['remark']  = Request::param('remark/s', '');

        validate(CommentValidate::class)->scene('add')->check($param);

        $data = CommentService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['comment_id'] = Request::param('comment_id/d', '');
        $param['call']       = Request::param('call/s', '');
        $param['mobile']     = Request::param('mobile/s', '');
        $param['tel']        = Request::param('tel/s', '');
        $param['email']      = Request::param('email/s', '');
        $param['qq']         = Request::param('qq/s', '');
        $param['wechat']     = Request::param('wechat/s', '');
        $param['title']      = Request::param('title/s', '');
        $param['content']    = Request::param('content/s', '');
        $param['remark']     = Request::param('remark/s', '');

        validate(CommentValidate::class)->scene('edit')->check($param);

        $data = CommentService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\comment")
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
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\comment")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function isread()
    {
        $param['comment'] = Request::param('comment/a', '');

        validate(CommentValidate::class)->scene('isread')->check($param);

        $data = CommentService::isread($param['comment']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\search")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\cms\CommentModel\list")
     *      )
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

        validate(CommentValidate::class)->scene('sort')->check(['sort_field' => $sort_field, 'sort_value' => $sort_value]);

        $where[] = ['is_delete', '=', 1];
        if ($search_field && $search_value) {
            if ($search_field == 'comment_id') {
                $where[] = [$search_field, '=', $search_value];
            } elseif (in_array($search_field, ['is_read'])) {
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

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        } else {
            $order = ['delete_time' => 'desc'];
        }

        $field = '';

        $data = CommentService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\comment")
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
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\comment")
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
