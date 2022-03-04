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
 * @Apidoc\Sort("330")
 */
class Comment
{
    /**
     * @Apidoc\Title("留言列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="留言列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CommentModel\listReturn")
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

        if ($search_field && $search_value) {
            if (in_array($search_field, ['comment_id'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['is_unread'])) {
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

        $data = CommentService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言信息")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\id")
     * @Apidoc\Returned(ref="app\common\model\cms\CommentModel\infoReturn")
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
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\addParam")
     * @Apidoc\Param("call", mock="@cname")
     * @Apidoc\Param("mobile", mock="@phone")
     * @Apidoc\Param("title", mock="@ctitle(8, 32)")
     * @Apidoc\Param("content", mock="@cparagraph(8, 32)")
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
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\editParam")
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
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言已读")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function isread()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(CommentValidate::class)->scene('isread')->check($param);

        $data = CommentService::isread($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="留言列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CommentModel\listReturn")
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

        if ($search_field && $search_value) {
            if (in_array($search_field, ['comment_id'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['is_unread'])) {
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
        $where[] = ['is_delete', '=', 1];
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

        $data = CommentService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(CommentValidate::class)->scene('reco')->check($param);

        $data = CommentService::recoverReco($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::recoverDele($param['ids']);

        return success($data);
    }
}
