<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\UserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户管理")
 * @Apidoc\Group("adminAuth")
 * @Apidoc\Sort("630")
 */
class User extends BaseController
{
    /**
     * @Apidoc\Title("用户列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\admin\UserModel\listReturn", type="array", desc="用户列表")
     */
    public function list()
    {
        $where = $this->where([], 'admin_user_id,is_super,is_disable');

        $data = UserService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("用户信息")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\infoReturn")
     */
    public function info()
    {
        $param['admin_user_id'] = $this->param('admin_user_id/d', '');

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
        $param['avatar_id'] = $this->param('avatar_id/d', 0);
        $param['username']  = $this->param('username/s', '');
        $param['nickname']  = $this->param('nickname/s', '');
        $param['password']  = $this->param('password/s', '');
        $param['email']     = $this->param('email/s', '');
        $param['phone']     = $this->param('phone/s', '');
        $param['remark']    = $this->param('remark/s', '');
        $param['sort']      = $this->param('sort/d', 250);

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
        $param['admin_user_id'] = $this->param('admin_user_id/d', '');
        $param['avatar_id']     = $this->param('avatar_id/d', 0);
        $param['username']      = $this->param('username/s', '');
        $param['nickname']      = $this->param('nickname/s', '');
        $param['email']         = $this->param('email/s', '');
        $param['phone']         = $this->param('phone/s', '');
        $param['remark']        = $this->param('remark/s', '');
        $param['sort']          = $this->param('sort/d', 250);

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
        $param['ids'] = $this->param('ids/a', '');

        validate(UserValidate::class)->scene('dele')->check($param);

        $data = UserService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户分配权限")
     * @Apidoc\Method("GET,POST")
     * @Apidoc\Desc("GET获取权限信息，POST提交权限信息")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\ruleParam")
     */
    public function rule()
    {
        $param['admin_user_id'] = $this->param('admin_user_id/d', '');

        validate(UserValidate::class)->scene('rule')->check($param);

        if ($this->request->isGet()) {
            $data = UserService::rule($param);
        } else {
            $param['admin_role_ids'] = $this->param('admin_role_ids/a', '');
            $param['admin_menu_ids'] = $this->param('admin_menu_ids/a', '');

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
        $param['ids']      = $this->param('ids/a', '');
        $param['password'] = $this->param('password/s', '');

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
        $param['ids']      = $this->param('ids/a', '');
        $param['is_super'] = $this->param('is_super/d', 0);

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
        $param['ids']        = $this->param('ids/a', '');
        $param['is_disable'] = $this->param('is_disable/d', 0);

        validate(UserValidate::class)->scene('disable')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }
}
