<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-07-16
 */

namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\UserCenterValidate;
use app\common\service\admin\UserCenterService;
use app\common\service\admin\MenuService;
use app\common\service\UploadService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("个人中心")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("50")
 */
class UserCenter
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\admin\UserModel\info")
     * )
     */
    public function info()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserCenterValidate::class)->scene('info')->check($param);

        $data = UserCenterService::info($param['admin_user_id']);

        if ($data['is_delete'] == 1) {
            exception('账号已被删除！');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("修改信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['avatar']        = Request::param('avatar/s', '');
        $param['username']      = Request::param('username/s', '');
        $param['nickname']      = Request::param('nickname/s', '');
        $param['phone']         = Request::param('phone/s', '');
        $param['email']         = Request::param('email/s', '');

        validate(UserCenterValidate::class)->scene('edit')->check($param);

        $data = UserCenterService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function pwd()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['password_old']  = Request::param('password_old/s', '');
        $param['password_new']  = Request::param('password_new/s', '');

        validate(UserCenterValidate::class)->scene('pwd')->check($param);

        $data = UserCenterService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("上传头像")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="paramFile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnFile")
     */
    public function avatar()
    {
        $param['avatar'] = Request::file('file');

        validate(UserCenterValidate::class)->scene('avatar')->check($param);

        $data = UploadService::upload($param['avatar'], 'admin/user');

        return success($data);
    }

    /**
     * @Apidoc\Title("我的日志")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\log")
     * @Apidoc\Param("request_keyword", type="string", default="", desc="请求地区/ip/isp")
     * @Apidoc\Param("menu_keyword", type="string", default="", desc="菜单链接/名称")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\admin\UserLogModel\list")
     *      )
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

        validate(UserCenterValidate::class)->scene('log')->check(['admin_user_id' => $admin_user_id]);

        $where   = [];
        $where[] = ['admin_user_id', '=', $admin_user_id];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($menu_keyword) {
            $admin_menu     = MenuService::likeQuery($menu_keyword);
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

        $data = UserCenterService::log($where, $page, $limit, $order);

        return success($data);
    }
}
