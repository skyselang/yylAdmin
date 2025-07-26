<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\system\MenuValidate as Validate;
use app\common\service\system\MenuService as Service;
use app\common\model\system\MenuModel as Model;
use app\common\service\system\RoleService;

/**
 * @Apidoc\Title("lang(菜单管理)")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("200")
 */
class Menu extends BaseController
{
    /**
     * 验证器
     */
    protected $validate = Validate::class;

    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    /**
     * @Apidoc\Title("lang(菜单列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function list()
    {
        $where  = $this->where(where_delete());
        $order  = $this->order();
        $islist = $this->param('islist');
        $param  = ['islist' => $islist, 'search_mode' => $this->param('search_mode')];

        $basedata = $this->service::basedata(true);
        if ($islist) {
            $data['list']  = $this->service::list('list', $where, $order, '', 0, 0, $param);
            $data['count'] = count($data['list']);
        } else {
            $data['list']  = $this->service::list('tree', $where, $order);
            $data['count'] = count($this->service::list('list', $where, $order));
            if (count($where) > 1) {
                $list = tree_to_list($data['list']);
                $all  = tree_to_list($basedata['trees']);
                $pk   = $this->model()->getPk();
                $pid  = $this->model()->pidk;
                $ids  = [];
                foreach ($list as $val) {
                    $pids = children_parent_key($all, $val[$pk], $pk, $pid);
                    $cids = parent_children_key($all, $val[$pk], $pk, $pid);
                    $ids  = array_merge($ids, $pids, $cids);
                }
                $data['list'] = $this->service::list('tree', [[$pk, 'in', $ids], where_delete()], $order);
            }
        }
        $data['basedata'] = $basedata;

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单信息)")
     * @Apidoc\Query(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function info()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '']);

        validate($this->validate)->scene('info')->check($param);

        $data = $this->service::info($param[$pk]);
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单添加)")
     * @Apidoc\Desc("lang(get获取基础数据，post提交添加)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Param(ref={Service::class,"add"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $data['basedata'] = $this->service::basedata();
            return success($data);
        }

        $pk    = $this->model()->getPk();
        $param = $this->params($this->service::$editField);
        unset($param[$pk]);

        validate($this->validate)->scene('add')->check($param);

        $data = $this->service::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单修改)")
     * @Apidoc\Desc("lang(get获取数据，post提交修改)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Query(ref={Service::class,"info"})
     * @Apidoc\Param(ref={Service::class,"edit"})
     * @Apidoc\Returned(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Param(ref={Service::class,"edit"})
     */
    public function edit()
    {
        $pk = $this->model()->getPk();

        if ($this->request->isGet()) {
            $param = $this->params([$pk => '']);

            validate($this->validate)->scene('info')->check($param);

            $data = $this->service::info($param[$pk]);
            $data['basedata'] = $this->service::basedata();

            return success($data);
        }

        $param = $this->params($this->service::$editField);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param[$pk], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单删除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"dele"})
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate($this->validate)->scene('dele')->check($param);

        $data = $this->service::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单是否禁用)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"disable"})
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate($this->validate)->scene('disable')->check($param);

        $data = $this->service::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单批量修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"update"})
     */
    public function update()
    {
        $param = $this->params(['ids/a' => [], 'field/s' => '', 'value']);

        validate($this->validate)->scene('update')->check($param);

        $data = $this->service::update($param['ids'], $param['field'], $param['value']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单导出)")
     * @Apidoc\Desc("lang(post提交导出，get下载导出文件)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Query(ref={Service::class,"export"})
     * @Apidoc\Param(ref={Service::class,"export"})
     * @Apidoc\Returned(ref={Service::class,"export"})
     */
    public function export()
    {
        if ($this->request->isGet()) {
            $param = $this->params(['file_path/s' => '', 'file_name/s' => '']);
            return download($param['file_path'], $param['file_name']);
        }

        $ids   = $this->param('ids/a', []);
        $where = [];
        if ($ids) {
            $model = $this->model();
            $pk    = $model->getPk();
            $where = [$pk, 'in', $ids];
        }
        $param['remark'] = $this->param('remark/s');
        $param['param']  = ['where' => $this->where(where_delete($where)), 'order' => $this->order()];

        $data = $this->service::export($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单修改上级)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editPid"})
     */
    public function editPid()
    {
        $param = $this->params(['ids/a' => [], 'menu_pid/d' => 0]);

        validate($this->validate)->scene('editPid')->check($param);

        $data = $this->service::editPid($param['ids'], $param['menu_pid']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单修改免登)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editUnlogin"})
     */
    public function editUnlogin()
    {
        $param = $this->params(['ids/a' => [], 'is_unlogin/d' => 0]);

        validate($this->validate)->scene('editUnlogin')->check($param);

        $data = $this->service::editUnlogin($param['ids'], $param['is_unlogin']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单修改免权)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editUnauth"})
     */
    public function editUnauth()
    {
        $param = $this->params(['ids/a' => [], 'is_unauth/d' => 0]);

        validate($this->validate)->scene('editUnauth')->check($param);

        $data = $this->service::editUnauth($param['ids'], $param['is_unauth']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单修改免限)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editUnrate"})
     */
    public function editUnrate()
    {
        $param = $this->params(['ids/a' => [], 'is_unrate/d' => 0]);

        validate($this->validate)->scene('editUnrate')->check($param);

        $data = $this->service::editUnrate($param['ids'], $param['is_unrate']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单角色列表)")
     * @Apidoc\Query(ref={Service::class,"roleList"})
     * @Apidoc\Returned(ref={RoleService::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"roleList"})
     */
    public function roleList()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '']);

        validate($this->validate)->scene('roleList')->check($param);

        $where = $this->where(where_delete([$pk, '=', $param[$pk]]));

        $data = $this->service::roleList($where, $this->page(), $this->limit(), $this->order());
        $data['basedata'] = RoleService::basedata(true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单角色解除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"roleLift"})
     */
    public function roleLift()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => [], 'role_ids/a' => []]);

        validate($this->validate)->scene('roleLift')->check($param);

        $data = $this->service::roleLift($param[$pk], $param['role_ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(菜单重置ID)")
     * @Apidoc\Method("POST")
     */
    public function resetId()
    {
        $data = $this->service::resetId();

        return success($data);
    }
}
