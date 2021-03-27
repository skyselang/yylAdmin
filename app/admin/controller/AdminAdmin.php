<?php
/*
 * @Description  : 管理员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2021-03-24
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminAdminValidate;
use app\admin\service\AdminAdminService;

class AdminAdmin
{
    /**
     * 管理员列表
     *
     * @method GET
     * 
     * @return json
     */
    public function adminList()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $username   = Request::param('username/s', '');
        $nickname   = Request::param('nickname/s', '');
        $email      = Request::param('email/s', '');

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($nickname) {
            $where[] = ['nickname', 'like', '%' . $nickname . '%'];
        }
        if ($email) {
            $where[] = ['email', 'like', '%' . $email . '%'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = AdminAdminService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * 管理员信息
     *
     * @method GET
     * 
     * @return json
     */
    public function adminInfo()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');

        validate(AdminAdminValidate::class)->scene('admin_id')->check($param);

        $data = AdminAdminService::info($param['admin_admin_id']);

        if ($data['is_delete'] == 1) {
            exception('管理员已被删除：' . $param['admin_admin_id']);
        }

        return success($data);
    }

    /**
     * 管理员添加
     *
     * @method POST
     * 
     * @return json
     */
    public function adminAdd()
    {
        $param['username'] = Request::param('username/s', '');
        $param['nickname'] = Request::param('nickname/s', '');
        $param['password'] = Request::param('password/s', '');
        $param['email']    = Request::param('email/s', '');
        $param['phone']    = Request::param('phone/s', '');
        $param['remark']   = Request::param('remark/s', '');
        $param['sort']     = Request::param('sort/d', 200);

        validate(AdminAdminValidate::class)->scene('admin_add')->check($param);

        $data = AdminAdminService::add($param);

        return success($data);
    }

    /**
     * 管理员修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function adminEdit()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');

        if (Request::isGet()) {
            validate(AdminAdminValidate::class)->scene('admin_id')->check($param);

            $data = AdminAdminService::edit($param);

            if ($data['admin_admin']['is_delete'] == 1) {
                exception('管理员已被删除：' . $param['admin_admin_id']);
            }
        } else {
            $param['username'] = Request::param('username/s', '');
            $param['nickname'] = Request::param('nickname/s', '');
            $param['email']    = Request::param('email/s', '');
            $param['phone']    = Request::param('phone/s', '');
            $param['remark']   = Request::param('remark/s', '');
            $param['sort']     = Request::param('sort/d', 200);

            validate(AdminAdminValidate::class)->scene('admin_edit')->check($param);

            $data = AdminAdminService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 管理员删除
     *
     * @method POST
     * 
     * @return json
     */
    public function adminDele()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');

        validate(AdminAdminValidate::class)->scene('admin_dele')->check($param);

        $data = AdminAdminService::dele($param['admin_admin_id']);

        return success($data);
    }

    /**
     * 管理员更换头像
     *
     * @method POST
     * 
     * @return json
     */
    public function adminAvatar()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');
        $param['avatar']         = Request::file('avatar_file');

        validate(AdminAdminValidate::class)->scene('admin_avatar')->check($param);

        $data = AdminAdminService::avatar($param);

        return success($data);
    }

    /**
     * 管理员密码重置
     *
     * @method POST
     * 
     * @return json
     */
    public function adminPwd()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');
        $param['password']       = Request::param('password/s', '');

        validate(AdminAdminValidate::class)->scene('admin_pwd')->check($param);

        $data = AdminAdminService::pwd($param);

        return success($data);
    }

    /**
     * 管理员权限分配
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function adminRule()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');

        if (Request::isGet()) {
            validate(AdminAdminValidate::class)->scene('admin_id')->check($param);

            $data = AdminAdminService::rule($param);
        } else {
            $param['admin_role_ids'] = Request::param('admin_role_ids/a', []);
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

            validate(AdminAdminValidate::class)->scene('admin_rule')->check($param);

            $data = AdminAdminService::rule($param, 'post');
        }

        return success($data);
    }

    /**
     * 管理员是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function adminDisable()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');
        $param['is_disable']     = Request::param('is_disable/d', 0);

        validate(AdminAdminValidate::class)->scene('admin_disable')->check($param);

        $data = AdminAdminService::disable($param);

        return success($data);
    }

    /**
     * 管理员是否超管
     *
     * @method POST
     * 
     * @return json
     */
    public function adminAdmin()
    {
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');
        $param['is_admin']       = Request::param('is_admin/d', 0);

        validate(AdminAdminValidate::class)->scene('admin_admin')->check($param);

        $data = AdminAdminService::admin($param);

        return success($data);
    }
}
