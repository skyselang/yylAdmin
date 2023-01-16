<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\validate\system\UserValidate;
use app\common\service\system\UserService;
use app\common\service\system\DeptService;
use app\common\service\system\PostService;
use app\common\service\system\RoleService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户管理")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("500")
 */
class User extends BaseController
{
    /**
     * @Apidoc\Title("用户列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\system\UserModel", type="array", desc="用户列表", field="user_id,nickname,username,sort,is_super,is_disable,create_time,update_time")
     * @Apidoc\Returned("dept", ref="app\common\model\system\DeptModel", type="tree", desc="部门树形", field="dept_id,dept_pid,dept_name")
     * @Apidoc\Returned("post", ref="app\common\model\system\PostModel", type="tree", desc="职位树形", field="post_id,post_pid,post_name")
     * @Apidoc\Returned("role", ref="app\common\model\system\RoleModel", type="array", desc="角色列表", field="role_id,role_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = UserService::list($where, $this->page(), $this->limit(), $this->order());

        $data['dept']  = DeptService::list('tree', [where_delete()], [], 'dept_id,dept_pid,dept_name');
        $data['post']  = PostService::list('tree', [where_delete()], [], 'post_id,post_pid,post_name');
        $data['role']  = RoleService::list([where_delete()], 0, 0, [], 'role_id,role_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("用户信息")
     * @Apidoc\Query(ref="app\common\model\system\UserModel", field="user_id")
     * @Apidoc\Returned(ref="app\common\model\system\UserModel")
     */
    public function info()
    {
        $param['user_id'] = $this->request->param('user_id/d', 0);

        validate(UserValidate::class)->scene('info')->check($param);

        $data = UserService::info($param['user_id'], true, true);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="avatar_id,nickname,username,password,phone,email,remark,sort")
     */
    public function add()
    {
        $param['avatar_id'] = $this->request->param('avatar_id/d', 0);
        $param['nickname']  = $this->request->param('nickname/s', '');
        $param['username']  = $this->request->param('username/s', '');
        $param['password']  = $this->request->param('password/s', '');
        $param['phone']     = $this->request->param('phone/s', '');
        $param['email']     = $this->request->param('email/s', '');
        $param['remark']    = $this->request->param('remark/s', '');
        $param['sort']      = $this->request->param('sort/d', 250);

        validate(UserValidate::class)->scene('add')->check($param);

        $data = UserService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="user_id,avatar_id,nickname,username,password,phone,email,remark,sort")
     */
    public function edit()
    {
        $param['user_id']   = $this->request->param('user_id/d', 0);
        $param['avatar_id'] = $this->request->param('avatar_id/d', 0);
        $param['nickname']  = $this->request->param('nickname/s', '');
        $param['username']  = $this->request->param('username/s', '');
        $param['phone']     = $this->request->param('phone/s', '');
        $param['email']     = $this->request->param('email/s', '');
        $param['remark']    = $this->request->param('remark/s', '');
        $param['sort']      = $this->request->param('sort/d', 250);

        validate(UserValidate::class)->scene('edit')->check($param);

        $data = UserService::edit($param['user_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(UserValidate::class)->scene('dele')->check($param);

        $data = UserService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户修改部门")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("dept_ids", type="array", desc="部门id")
     */
    public function editdept()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['dept_ids'] = $this->request->param('dept_ids/a', []);

        validate(UserValidate::class)->scene('editdept')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户修改职位")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("post_ids", type="array", desc="职位id")
     */
    public function editpost()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['post_ids'] = $this->request->param('post_ids/a', []);

        validate(UserValidate::class)->scene('editpost')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户修改角色")
     * @Apidoc\Method("GET,POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("role_ids", type="array", desc="角色id")
     */
    public function editrole()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['role_ids'] = $this->request->param('role_ids/a', []);

        validate(UserValidate::class)->scene('editrole')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="password")
     */
    public function repwd()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['password'] = $this->request->param('password/s', '');

        validate(UserValidate::class)->scene('repwd')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户是否超管")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="is_super")
     */
    public function super()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['is_super'] = $this->request->param('is_super/d', 0);

        validate(UserValidate::class)->scene('super')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(UserValidate::class)->scene('disable')->check($param);

        $data = UserService::edit($param['ids'], $param);

        return success($data);
    }
}
