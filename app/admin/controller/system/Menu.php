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
use app\common\validate\system\MenuValidate;
use app\common\service\system\MenuService;
use app\common\service\system\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("菜单管理")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("100")
 */
class Menu extends BaseController
{
    /**
     * @Apidoc\Title("菜单列表")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned("list", ref="app\common\model\system\MenuModel", type="tree", desc="菜单树形", field="menu_id,menu_pid,menu_name,menu_type,meta_icon,menu_url,path,name,component,hidden,sort,is_unlogin,is_unauth,is_unrate,is_disable")
     * @Apidoc\Returned("tree", ref="app\common\model\system\MenuModel", type="tree", desc="菜单树形", field="menu_id,menu_pid,menu_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data['list']  = MenuService::list('tree', $where);
        $data['tree']  = MenuService::list('tree', [where_delete()], [], 'menu_id,menu_pid,menu_name');
        $data['types'] = SettingService::menu_types();
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单信息")
     * @Apidoc\Query(ref="app\common\model\system\MenuModel", field="menu_id")
     * @Apidoc\Returned(ref="app\common\model\system\MenuModel")
     */
    public function info()
    {
        $param['menu_id'] = $this->request->param('menu_id/d', 0);

        validate(MenuValidate::class)->scene('info')->check($param);

        $data = MenuService::info($param['menu_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="menu_pid,menu_type,meta_icon,menu_name,menu_url,path,component,name,meta_query,hidden,sort,is_unlogin,is_unauth,is_unrate,is_disable")
     */
    public function add()
    {
        $param['menu_pid']   = $this->request->param('menu_pid/d', 0);
        $param['menu_type']  = $this->request->param('menu_type/d', SettingService::MENU_TYPE_CATALOGUE);
        $param['meta_icon']  = $this->request->param('meta_icon/s', '');
        $param['menu_name']  = $this->request->param('menu_name/s', '');
        $param['menu_url']   = $this->request->param('menu_url/s', '');
        $param['path']       = $this->request->param('path/s', '');
        $param['component']  = $this->request->param('component/s', '');
        $param['name']       = $this->request->param('name/s', '');
        $param['meta_query'] = $this->request->param('meta_query/s', '');
        $param['hidden']     = $this->request->param('hidden/d', 0);
        $param['sort']       = $this->request->param('sort/d', 250);
        $param['add_info']   = $this->request->param('add_info/b', false);
        $param['add_add']    = $this->request->param('add_add/b', false);
        $param['add_edit']   = $this->request->param('add_edit/b', false);
        $param['add_dele']   = $this->request->param('add_dele/b', false);

        validate(MenuValidate::class)->scene('add')->check($param);

        $data = MenuService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="menu_id,menu_pid,menu_type,meta_icon,menu_name,menu_url,path,component,name,meta_query,hidden,sort,is_unlogin,is_unauth,is_unrate,is_disable")
     */
    public function edit()
    {
        $param['menu_id']    = $this->request->param('menu_id/d', 0);
        $param['menu_pid']   = $this->request->param('menu_pid/d', 0);
        $param['menu_type']  = $this->request->param('menu_type/d', SettingService::MENU_TYPE_CATALOGUE);
        $param['meta_icon']  = $this->request->param('meta_icon/s', '');
        $param['menu_name']  = $this->request->param('menu_name/s', '');
        $param['menu_url']   = $this->request->param('menu_url/s', '');
        $param['path']       = $this->request->param('path/s', '');
        $param['component']  = $this->request->param('component/s', '');
        $param['name']       = $this->request->param('name/s', '');
        $param['meta_query'] = $this->request->param('meta_query/s', '');
        $param['hidden']     = $this->request->param('hidden/d', 0);
        $param['sort']       = $this->request->param('sort/d', 250);
        $param['add_info']   = $this->request->param('add_info/b', false);
        $param['add_add']    = $this->request->param('add_add/b', false);
        $param['add_edit']   = $this->request->param('add_edit/b', false);
        $param['add_dele']   = $this->request->param('add_dele/b', false);
        $param['edit_info']  = $this->request->param('edit_info/b', false);
        $param['edit_add']   = $this->request->param('edit_add/b', false);
        $param['edit_edit']  = $this->request->param('edit_edit/b', false);
        $param['edit_dele']  = $this->request->param('edit_dele/b', false);

        validate(MenuValidate::class)->scene('edit')->check($param);

        $data = MenuService::edit($param['menu_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(MenuValidate::class)->scene('dele')->check($param);

        $data = MenuService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单修改排序")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="sort")
     */
    public function editsort()
    {
        $param['ids']         = $this->request->param('ids/a', []);
        $param['sort']        = $this->request->param('sort/d', 250);
        $param['sort_incdec'] = $this->request->param('sort_incdec/d', 0);

        validate(MenuValidate::class)->scene('editsort')->check($param);

        if ($param['sort_incdec']) {
            foreach ($param['ids'] as $k => $id) {
                $data[] = MenuService::update([$id], ['sort' => $param['sort_incdec'] * $k + $param['sort']]);
            }
        } else {
            $data = MenuService::update($param['ids'], $param);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="menu_pid")
     */
    public function editpid()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['menu_pid'] = $this->request->param('menu_pid/d', 0);

        validate(MenuValidate::class)->scene('editpid')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否免登")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="is_unlogin")
     */
    public function unlogin()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_unlogin'] = $this->request->param('is_unlogin/d', 0);

        validate(MenuValidate::class)->scene('unlogin')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否免权")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="is_unauth")
     */
    public function unauth()
    {
        $param['ids']       = $this->request->param('ids/a', []);
        $param['is_unauth'] = $this->request->param('is_unauth/d', 0);

        validate(MenuValidate::class)->scene('unauth')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否免限")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="is_unrate")
     */
    public function unrate()
    {
        $param['ids']       = $this->request->param('ids/a', []);
        $param['is_unrate'] = $this->request->param('is_unrate/d', 0);

        validate(MenuValidate::class)->scene('unrate')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="is_hidden")
     */
    public function hidden()
    {
        $param['ids']    = $this->request->param('ids/a', []);
        $param['hidden'] = $this->request->param('hidden/d', 0);

        validate(MenuValidate::class)->scene('hidden')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(MenuValidate::class)->scene('disable')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\system\MenuModel", field="menu_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\system\RoleModel", type="array", desc="角色列表", field="role_id,role_name,role_desc,sort,is_disable,create_time,update_time")
     */
    public function role()
    {
        $param['menu_id'] = $this->request->param('menu_id/d', 0);

        validate(MenuValidate::class)->scene('role')->check($param);

        $where = $this->where(where_delete(['menu_ids', 'in', [$param['menu_id']]]));

        $data = MenuService::role($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\MenuModel", field="menu_id")
     * @Apidoc\Param("role_ids", type="array", require=false, desc="角色id，为空则解除所有菜单")
     */
    public function roleRemove()
    {
        $param['menu_id']  = $this->request->param('menu_id/a', []);
        $param['role_ids'] = $this->request->param('role_ids/a', []);

        validate(MenuValidate::class)->scene('roleRemove')->check($param);

        $data = MenuService::roleRemove($param['menu_id'], $param['role_ids']);

        return success($data);
    }
}
