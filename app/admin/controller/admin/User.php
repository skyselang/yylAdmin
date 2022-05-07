<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\UserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户管理")
 * @Apidoc\Group("adminAuth")
 * @Apidoc\Sort("630")
 */
class User
{
    /**
     * @Apidoc\Title("用户列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn"),
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\UserModel\listReturn")
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

        $where = [];
        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['admin_user_id', 'is_super', 'is_disable'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
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

        $data = UserService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户信息")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\infoReturn")
     */
    public function info()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(UserValidate::class)->scene('info')->check($param);

        $data = UserService::info($param['admin_user_id']);
        if ($data['is_delete'] == 1) {
            exception('用户已被删除：' . $param['admin_user_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\addParam")
     */
    public function add()
    {
        $param['avatar_id'] = Request::param('avatar_id/d', 0);
        $param['username']  = Request::param('username/s', '');
        $param['nickname']  = Request::param('nickname/s', '');
        $param['password']  = Request::param('password/s', '');
        $param['email']     = Request::param('email/s', '');
        $param['phone']     = Request::param('phone/s', '');
        $param['remark']    = Request::param('remark/s', '');
        $param['sort']      = Request::param('sort/d', 250);

        validate(UserValidate::class)->scene('add')->check($param);

        $data = UserService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\editParam")
     */
    public function edit()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['avatar_id']     = Request::param('avatar_id/d', 0);
        $param['username']      = Request::param('username/s', '');
        $param['nickname']      = Request::param('nickname/s', '');
        $param['email']         = Request::param('email/s', '');
        $param['phone']         = Request::param('phone/s', '');
        $param['remark']        = Request::param('remark/s', '');
        $param['sort']          = Request::param('sort/d', 250);

        validate(UserValidate::class)->scene('edit')->check($param);

        $data = UserService::edit($param['admin_user_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(UserValidate::class)->scene('dele')->check($param);

        $data = UserService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户分配权限")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\ruleParam")
     */
    public function rule()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(UserValidate::class)->scene('rule')->check($param);

        if (Request::isGet()) {
            $data = UserService::rule($param);
        } else {
            $param['admin_role_ids'] = Request::param('admin_role_ids/a', '');
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', '');

            $data = UserService::rule($param, 'post');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\password")
     */
    public function pwd()
    {
        $param['ids']      = Request::param('ids/a', '');
        $param['password'] = Request::param('password/s', '');

        validate(UserValidate::class)->scene('pwd')->check($param);

        $data = UserService::edit($param['ids'], ['password' => md5($param['password'])]);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户是否超管")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\is_super")
     */
    public function super()
    {
        $param['ids']      = Request::param('ids/a', '');
        $param['is_super'] = Request::param('is_super/d', 0);

        validate(UserValidate::class)->scene('super')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(UserValidate::class)->scene('disable')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }
}
