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
        $data['types'] = SettingService::menuTypes();
        $data['exps']  = where_exps();
        $data['where'] = $where;

        if (count($where) > 1) {
            $list = tree_to_list($data['list']);
            $all  = tree_to_list($data['tree']);
            $pk   = 'menu_id';
            $pid  = 'menu_pid';
            $ids  = [];
            foreach ($list as $val) {
                $pids = children_parent_ids($all, $val[$pk], $pk, $pid);
                $cids = parent_children_ids($all, $val[$pk], $pk, $pid);
                $ids  = array_merge($ids, $pids, $cids);
            }
            $data['list'] = MenuService::list('tree', [[$pk, 'in', $ids], where_delete()]);
        }

        $menu = MenuService::list('list', $where, [], 'menu_id');
        $data['count'] = count($menu);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单信息")
     * @Apidoc\Query(ref="app\common\model\system\MenuModel", field="menu_id")
     * @Apidoc\Returned(ref="app\common\model\system\MenuModel")
     */
    public function info()
    {
        $param = $this->params(['menu_id/d' => '']);

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
        $param = $this->params(MenuService::$edit_field);

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
        $param = $this->params(MenuService::$edit_field);

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
        $param = $this->params(['ids/a' => []]);

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
        $param = $this->params(['ids/a' => [], 'sort/d' => 250, 'sort_incdec/d' => 0]);

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
        $param = $this->params(['ids/a' => [], 'menu_pid/d' => 0]);

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
        $param = $this->params(['ids/a' => [], 'is_unlogin/d' => 0]);

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
        $param = $this->params(['ids/a' => [], 'is_unauth/d' => 0]);

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
        $param = $this->params(['ids/a' => [], 'is_unrate/d' => 0]);

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
        $param = $this->params(['ids/a' => [], 'hidden/d' => 0]);

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
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

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
        $param = $this->params(['menu_id/d' => '']);

        validate(MenuValidate::class)->scene('role')->check($param);

        $where = $this->where(where_delete(['menu_ids', 'in', [$param['menu_id']]]));

        $data = MenuService::role($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("menu_id", type="array", require=true, desc="菜单id")
     * @Apidoc\Param("role_ids", type="array", require=false, desc="角色id，为空则解除所有菜单")
     */
    public function roleRemove()
    {
        $param = $this->params(['menu_id/a' => [], 'role_ids/a' => []]);

        validate(MenuValidate::class)->scene('roleRemove')->check($param);

        $data = MenuService::roleRemove($param['menu_id'], $param['role_ids']);

        return success($data);
    }
}
