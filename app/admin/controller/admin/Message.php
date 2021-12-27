<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 消息管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\MessageValidate;
use app\common\service\admin\MessageService;
use app\common\service\admin\UserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("消息管理")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("705")
 */
class Message
{
    /**
     * @Apidoc\Title("消息列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="消息列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\MessageModel\listReturn")
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
            if ($search_field == 'admin_message_id' || $search_field == 'admin_user_id') {
                $exp = strstr($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $exp, $search_value];
            } elseif ($search_field == 'admin_user') {
                $exp = strstr($search_value, ',') ? 'in' : '=';
                $where_user[] = ['username', $exp, $search_value];
                $admin_user = UserService::list($where_user, 1, 9999);
                $admin_user_ids = array_column($admin_user['list'], 'admin_user_id');
                $where[] = ['admin_user_id', 'in', $admin_user_ids];
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

        $data = MessageService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("消息信息")
     * @Apidoc\Param(ref="app\common\model\admin\MessageModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\MessageModel\infoReturn")
     */
    public function info()
    {
        $param['admin_message_id'] = Request::param('admin_message_id/d', '');

        validate(MessageValidate::class)->scene('info')->check($param);

        $data = MessageService::info($param['admin_message_id']);
        if ($data['is_delete'] == 1) {
            exception('消息已被删除：' . $param['admin_message_id']);
        }

        unset($data['password'], $data['token']);

        return success($data);
    }

    /**
     * @Apidoc\Title("消息添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\MessageModel\addParam")
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

        validate(MessageValidate::class)->scene('add')->check($param);

        $data = MessageService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("消息修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\MessageModel\editParam")
     */
    public function edit()
    {
        $param['admin_message_id'] = Request::param('admin_message_id/d', '');
        $param['title']            = Request::param('title/s', '');
        $param['color']            = Request::param('color/s', '#606266');
        $param['type']             = Request::param('type/d', 1);
        $param['sort']             = Request::param('sort/d', 250);
        $param['is_open']          = Request::param('is_open/d', 1);
        $param['open_time_start']  = Request::param('open_time_start/s', '');
        $param['open_time_end']    = Request::param('open_time_end/s', '');
        $param['intro']            = Request::param('intro/s', '');
        $param['content']          = Request::param('content/s', '');

        validate(MessageValidate::class)->scene('edit')->check($param);

        $data = MessageService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("消息删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("list", ref="app\common\model\admin\MessageModel\listReturn", type="array")
     */
    public function dele()
    {
        $param['list'] = Request::param('list/a', '');

        validate(MessageValidate::class)->scene('dele')->check($param);

        $data = MessageService::dele($param['list']);

        return success($data);
    }

    /**
     * @Apidoc\Title("消息是否开启")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("list", ref="app\common\model\admin\MessageModel\listReturn", type="array")
     * @Apidoc\Param(ref="app\common\model\admin\MessageModel\isopenParam")
     */
    public function isopen()
    {
        $param['list']    = Request::param('list/a', '');
        $param['is_open'] = Request::param('is_open/d', 0);

        validate(MessageValidate::class)->scene('isopen')->check($param);

        $data = MessageService::is_open($param['list'], $param['is_open']);

        return success($data);
    }
}
