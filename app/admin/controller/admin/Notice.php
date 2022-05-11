<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 公告管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\NoticeValidate;
use app\common\service\admin\NoticeService;
use app\common\model\admin\UserModel;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("公告管理")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("735")
 */
class Notice
{
    /**
     * @Apidoc\Title("公告列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\NoticeModel\listReturn")
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
            if (in_array($search_field, ['admin_notice_id', 'admin_user_id', 'is_open'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['username'])) {
                $UserModel = new UserModel();
                $UserPk = $UserModel->getPk();
                $user_exp = strpos($search_value, ',') ? 'in' : '=';
                $user_where[] = [$search_field, $user_exp, $search_value];
                $admin_user_ids = $UserModel->where($user_where)->column($UserPk);
                $where[] = [$UserPk, 'in', $admin_user_ids];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 0];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = NoticeService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告信息")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\NoticeModel\infoReturn")
     */
    public function info()
    {
        $param['admin_notice_id'] = Request::param('admin_notice_id/d', '');

        validate(NoticeValidate::class)->scene('info')->check($param);

        $data = NoticeService::info($param['admin_notice_id']);
        if ($data['is_delete'] == 1) {
            exception('公告已被删除：' . $param['admin_notice_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("公告添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\addParam")
     * @Apidoc\Param("title", mock="@ctitle(15, 32)")
     * @Apidoc\Param("open_time_start", mock="@now")
     * @Apidoc\Param("open_time_end", mock="@now")
     * @Apidoc\Param("intro", mock="@csentence(32, 64)")
     * @Apidoc\Param("content", mock="@cparagraph(64, 128)")
     */
    public function add()
    {
        $param['admin_user_id']   = admin_user_id();
        $param['title']           = Request::param('title/s', '');
        $param['color']           = Request::param('color/s', '#606266');
        $param['type']            = Request::param('type/d', 1);
        $param['sort']            = Request::param('sort/d', 250);
        $param['is_open']         = Request::param('is_open/d', 1);
        $param['open_time_start'] = Request::param('open_time_start/s', '');
        $param['open_time_end']   = Request::param('open_time_end/s', '');
        $param['intro']           = Request::param('intro/s', '');
        $param['content']         = Request::param('content/s', '');

        validate(NoticeValidate::class)->scene('add')->check($param);

        $data = NoticeService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\editParam")
     */
    public function edit()
    {
        $param['admin_notice_id'] = Request::param('admin_notice_id/d', '');
        $param['title']           = Request::param('title/s', '');
        $param['color']           = Request::param('color/s', '#606266');
        $param['type']            = Request::param('type/d', 1);
        $param['sort']            = Request::param('sort/d', 250);
        $param['is_open']         = Request::param('is_open/d', 1);
        $param['open_time_start'] = Request::param('open_time_start/s', '');
        $param['open_time_end']   = Request::param('open_time_end/s', '');
        $param['intro']           = Request::param('intro/s', '');
        $param['content']         = Request::param('content/s', '');

        validate(NoticeValidate::class)->scene('edit')->check($param);

        $data = NoticeService::edit($param['admin_notice_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(NoticeValidate::class)->scene('dele')->check($param);

        $data = NoticeService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告是否开启")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\is_open")
     */
    public function isopen()
    {
        $param['ids']     = Request::param('ids/a', '');
        $param['is_open'] = Request::param('is_open/d', 0);

        validate(NoticeValidate::class)->scene('isopen')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告开启时间")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\open_time_start")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\open_time_end")
     */
    public function opentime()
    {
        $param['ids']             = Request::param('ids/a', '');
        $param['open_time_start'] = Request::param('open_time_start/s', '');
        $param['open_time_end']   = Request::param('open_time_end/s', '');

        validate(NoticeValidate::class)->scene('opentime')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }
}
