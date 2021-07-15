<?php
/*
 * @Description  : 角色管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-30
 * @LastEditTime : 2021-07-14
 */

namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\RoleService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("角色管理")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("20")
 */
class Role
{
    /**
     * @Apidoc\Title("角色列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("role_name", type="string", default="", desc="角色名称")
     * @Apidoc\Param("role_desc", type="string", default="", desc="角色描述")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\admin\RoleModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s', '');
        $sort_type  = Request::param('sort_type/s', '');
        $role_name  = Request::param('role_name/s', '');
        $role_desc  = Request::param('role_desc/s', '');

        $where = [];
        if ($role_name) {
            $where[] = ['role_name', 'like', '%' . $role_name . '%'];
        }
        if ($role_desc) {
            $where[] = ['role_desc', 'like', '%' . $role_desc . '%'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = RoleService::list($where, $page, $limit, $order);

        return success($data);
    }
    
    /**
     * @Apidoc\Title("角色信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\admin\RoleModel\info")
     * )
     */
    public function info()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(RoleValidate::class)->scene('info')->check($param);

        $data = RoleService::info($param['admin_role_id']);

        if ($data['is_delete'] == 1) {
            exception('角色已被删除：' . $param['admin_role_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("角色添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['role_name']      = Request::param('role_name/s', '');
        $param['role_desc']      = Request::param('role_desc/s', '');
        $param['role_sort']      = Request::param('role_sort/d', 200);
        $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

        validate(RoleValidate::class)->scene('add')->check($param);

        $data = RoleService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */ 
    public function edit()
    {
        $param['admin_role_id']  = Request::param('admin_role_id/d', '');
        $param['role_name']      = Request::param('role_name/s', '');
        $param['role_desc']      = Request::param('role_desc/s', '');
        $param['role_sort']      = Request::param('role_sort/d', 200);
        $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

        validate(RoleValidate::class)->scene('edit')->check($param);

        $data = RoleService::edit($param, 'post');

        return success($data);
    }

    /**
     * @Apidoc\Title("角色删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */ 
    public function dele()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(RoleValidate::class)->scene('dele')->check($param);

        $data = RoleService::dele($param['admin_role_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\disable")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */ 
    public function disable()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

        validate(RoleValidate::class)->scene('disable')->check($param);

        $data = RoleService::disable($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色用户")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\admin\UserModel\user")
     *      )
     * )
     */ 
    public function user()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s ', '');
        $sort_type     = Request::param('sort_type/s', '');
        $admin_role_id = Request::param('admin_role_id/s', '');

        validate(RoleValidate::class)->scene('user')->check(['admin_role_id' => $admin_role_id]);

        $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = RoleService::user($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色用户解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */  
    public function userRemove()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(RoleValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = RoleService::userRemove($param);

        return success($data);
    }
}
