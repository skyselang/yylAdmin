<?php
/*
 * @Description  : 管理员个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-04-17
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminUserCenterValidate;
use app\common\service\AdminUserCenterService;
use app\common\service\AdminMenuService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("管理员个人中心")
 * @Apidoc\Group("admin")
 */
class AdminUserCenter
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("admin_user_id", type="int", require=true, desc="管理员id")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\AdminUserModel\info")
     * )
     */ 
    public function info()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminUserCenterValidate::class)->scene('info')->check($param);

        $data = AdminUserCenterService::info($param['admin_user_id']);

        if ($data['is_delete'] == 1) {
            exception('账号信息错误，请重新登录！');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("修改信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\edit")
     * @Apidoc\Returned(ref="return")
     */
    public function edit()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['username']      = Request::param('username/s', '');
        $param['nickname']      = Request::param('nickname/s', '');
        $param['phone']         = Request::param('phone/s', '');
        $param['email']         = Request::param('email/s', '');

        validate(AdminUserCenterValidate::class)->scene('edit')->check($param);

        $data = AdminUserCenterService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\id")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
     * @Apidoc\Returned(ref="return")
     */
    public function pwd()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['password_old']  = Request::param('password_old/s', '');
        $param['password_new']  = Request::param('password_new/s', '');

        validate(AdminUserCenterValidate::class)->scene('pwd')->check($param);

        $data = AdminUserCenterService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("更换头像")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\avatar")
     * @Apidoc\Returned(ref="return")
     */
    public function avatar()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['avatar']        = Request::file('avatar_file');

        validate(AdminUserCenterValidate::class)->scene('avatar')->check($param);

        $data = AdminUserCenterService::avatar($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("我的日志")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\AdminUserLogModel\log")
     * @Apidoc\Param("request_keyword", type="string", default="", desc="请求地区/ip/isp")
     * @Apidoc\Param("menu_keyword", type="string", default="", desc="菜单链接/名称")
     * @Apidoc\Param("create_time", type="array", default="[]", desc="开始与结束日期eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Returned(ref="return"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", ref="app\common\model\AdminUserLogModel\list")
     * )
     */
    public function log()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $log_type        = Request::param('log_type/d', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);
        $admin_user_id   = admin_user_id();

        validate(AdminUserCenterValidate::class)->scene('log')->check(['admin_user_id' => $admin_user_id]);

        $where   = [];
        $where[] = ['admin_user_id', '=', $admin_user_id];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($menu_keyword) {
            $admin_menu     = AdminMenuService::likeQuery($menu_keyword);
            $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
            $where[]        = ['admin_menu_id', 'in', $admin_menu_ids];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminUserCenterService::log($where, $page, $limit, $order);

        return success($data);
    }
}
