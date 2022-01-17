<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 个人中心控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\UserCenterValidate;
use app\common\service\admin\UserCenterService;
use app\common\model\admin\MenuModel;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("个人中心")
 * @Apidoc\Group("adminAuthority")
 * @Apidoc\Sort("650")
 */
class UserCenter
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\infoReturn")
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
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\editParam")
     */
    public function edit()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['avatar_id']     = Request::param('avatar_id/d', 0);
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
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
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
     * @Apidoc\Title("我的日志")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\listParam")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Param("response_code", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn"),
     * @Apidoc\Returned("list", type="array", desc="日志列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\UserLogModel\listReturn")
     * )
     */
    public function log()
    {
        $admin_user_id = admin_user_id();
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_value    = Request::param('sort_value/s', '');
        $date_field    = Request::param('date_field/s', '');
        $date_value    = Request::param('date_value/a', '');
        $search_field  = Request::param('search_field/s', '');
        $search_value  = Request::param('search_value/s', '');
        $log_type      = Request::param('log_type/d', '');

        validate(UserCenterValidate::class)->scene('log')->check(['admin_user_id' => $admin_user_id]);

        $where[] = ['admin_user_id', '=', $admin_user_id];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_value) {
            if (in_array($search_field, ['admin_user_log_id'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['menu_url', 'menu_name'])) {
                $MenuModel = new MenuModel();
                $MenuPk = $MenuModel->getPk();
                $menu_exp = strpos($search_value, ',') ? 'in' : '=';
                $menu_where[] = [$search_field, $menu_exp, $search_value];
                $admin_menu_ids = $MenuModel->where($menu_where)->column($MenuPk);
                $where[] = [$MenuPk, 'in', $admin_menu_ids];
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

        $data = UserCenterService::log($where, $page, $limit, $order);

        return success($data);
    }
}
