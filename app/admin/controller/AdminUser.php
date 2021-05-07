<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminUserValidate;
use app\common\service\AdminUserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户管理")
 * @Apidoc\Group("admin")
 */
class AdminUser
{
    /**
     * @Apidoc\Title("用户列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("username", type="string", default="", desc="账号")
     * @Apidoc\Param("nickname", type="string", default="", desc="昵称")
     * @Apidoc\Param("email", type="string", default="", desc="邮箱")
     * @Apidoc\Returned(ref="return"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\AdminUserModel\list")
     *      )
     * )
     */
    public function list()
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

        $data = AdminUserService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\id")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\AdminUserModel\info")
     * )
     */
    public function info()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('info')->check($param);

        $data = AdminUserService::info($param['admin_user_id']);

        if ($data['is_delete'] == 1) {
            exception('用户已被删除：' . $param['admin_user_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\add")
     * @Apidoc\Returned(ref="return")
     */
    public function add()
    {
        $param['username'] = Request::param('username/s', '');
        $param['nickname'] = Request::param('nickname/s', '');
        $param['password'] = Request::param('password/s', '');
        $param['email']    = Request::param('email/s', '');
        $param['phone']    = Request::param('phone/s', '');
        $param['remark']   = Request::param('remark/s', '');
        $param['sort']     = Request::param('sort/d', 200);

        validate(AdminUserValidate::class)->scene('add')->check($param);

        $data = AdminUserService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户修改")
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
        $param['email']         = Request::param('email/s', '');
        $param['phone']         = Request::param('phone/s', '');
        $param['remark']        = Request::param('remark/s', '');
        $param['sort']          = Request::param('sort/d', 200);

        validate(AdminUserValidate::class)->scene('edit')->check($param);

        $data = AdminUserService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\dele")
     * @Apidoc\Returned(ref="return")
     */
    public function dele()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('dele')->check($param);

        $data = AdminUserService::dele($param['admin_user_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户更换头像")
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

        validate(AdminUserValidate::class)->scene('avatar')->check($param);

        $data = AdminUserService::avatar($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\pwd")
     * @Apidoc\Returned(ref="return")
     */
    public function pwd()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['password']      = Request::param('password/s', '');

        validate(AdminUserValidate::class)->scene('pwd')->check($param);

        $data = AdminUserService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户分配权限")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\rule")
     * @Apidoc\Returned(ref="return")
     */
    public function rule()
    {
        $param['admin_user_id']  = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('rule')->check($param);

        if (Request::isGet()) {
            $data = AdminUserService::rule($param);
        } else {
            $param['admin_role_ids'] = Request::param('admin_role_ids/a', []);
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

            $data = AdminUserService::rule($param, 'post');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\disable")
     * @Apidoc\Returned(ref="return")
     */
    public function disable()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

        validate(AdminUserValidate::class)->scene('disable')->check($param);

        $data = AdminUserService::disable($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户是否超管")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\super")
     * @Apidoc\Returned(ref="return")
     */
    public function super()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['is_super']      = Request::param('is_super/d', 0);

        validate(AdminUserValidate::class)->scene('super')->check($param);

        $data = AdminUserService::super($param);

        return success($data);
    }
}
