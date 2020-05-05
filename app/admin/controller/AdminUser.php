<?php
/*
 * @Description  : 用户管理
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-03-26
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminUserService;
use app\admin\validate\AdminUserValidate;

class AdminUser
{
    /**
     * 用户列表
     *
     * @method GET
     * @return json
     */
    public function userList()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $order_field = Request::param('order_field/s ', '');
        $order_type  = Request::param('order_type/s', '');
        $username    = Request::param('username/s', '');
        $nickname    = Request::param('nickname/s', '');

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($nickname) {
            $where[] = ['nickname', 'like', '%' . $nickname . '%'];
        }

        $field = '';

        $order = [];
        if ($order_field && $order_type) {
            $order = [$order_field => $order_type];
        }

        $data = AdminUserService::list($where, $page, $limit, $field, $order);

        return success($data);
    }

    /**
     * 用户添加
     *
     * @method POST
     * @return json
     */
    public function userAdd()
    {
        $param = Request::only(
            [
                'username' => '',
                'nickname' => '',
                'password' => '',
                'remark'   => '',
                'sort'     => 200,
            ]
        );

        validate(AdminUserValidate::class)->scene('user_add')->check($param);

        $data = AdminUserService::add($param);

        return success($data);
    }

    /**
     * 用户修改
     *
     * @method POST
     * @return json
     */
    public function userEdit()
    {
        $param = Request::only([
            'admin_user_id' => '',
            'username'      => '',
            'nickname'      => '',
            'remark'        => '',
            'sort'          => 200,
        ]);

        validate(AdminUserValidate::class)->scene('user_edit')->check($param);

        $data = AdminUserService::edit($param);

        return success($data);
    }

    /**
     * 用户删除
     *
     * @method POST
     * @return json
     */
    public function userDele()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $data = AdminUserService::dele($admin_user_id);

        return success($data);
    }

    /**
     * 用户信息
     *
     * @method GET
     * @return json
     */
    public function userInfo()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $admin_user = AdminUserService::info($admin_user_id);

        return success($admin_user);
    }

    /**
     * 用户个人中心
     *
     * @method GET|POST
     * @return json
     */
    public function userCenter()
    {
        if (Request::isGet()) {
            $admin_user_id = Request::param('admin_user_id/d', '');

            validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

            $data = AdminUserService::info($admin_user_id);

            return success($data);
        }

        if (Request::isPost()) {
            $param = Request::only([
                'admin_user_id' => '',
                'username'      => '',
                'nickname'      => '',
                'password'      => '',
                'passwords'     => '',
            ]);

            if ($param['password']) {
                validate(AdminUserValidate::class)->scene('user_center2')->check($param);
            } else {
                validate(AdminUserValidate::class)->scene('user_center1')->check($param);
            }

            $data = AdminUserService::center($param);

            return success($data);
        }

        error('请求类型错误');
    }

    /**
     * 用户密码重置
     *
     * @method POST
     * @return json
     */
    public function userRepwd()
    {
        $param = Request::only([
            'admin_user_id' => '',
            'password'      => '',
        ]);

        validate(AdminUserValidate::class)->scene('user_repwd')->check($param);

        $data = AdminUserService::repwd($param);

        return success($data);
    }

    /**
     * 用户权限分配
     *
     * @method POST
     * @return json
     */
    public function userRule()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $admin_rule_ids = Request::param('admin_rule_ids/a', []);

        $param['admin_user_id'] = $admin_user_id;
        $param['admin_rule_ids'] = $admin_rule_ids;

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $data = AdminUserService::rule($param);

        return success($data);
    }

    /**
     * 用户是否禁用
     *
     * @method POST
     * @return json
     */
    public function userProhibit()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $is_prohibit = Request::param('is_prohibit/s', 0);

        $param['admin_user_id'] = $admin_user_id;
        $param['is_prohibit'] = $is_prohibit;

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $data = AdminUserService::prohibit($param);

        return success($data);
    }

    /**
     * 用户是否超管
     *
     * @method POST
     * @return json
     */
    public function userSuperAdmin()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $is_super_admin = Request::param('is_super_admin/s', 0);

        $param['admin_user_id'] = $admin_user_id;
        $param['is_super_admin'] = $is_super_admin;

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $data = AdminUserService::superAdmin($param);

        return success($data);
    }
}
