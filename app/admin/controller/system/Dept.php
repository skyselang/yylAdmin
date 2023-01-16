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
use app\common\validate\system\DeptValidate;
use app\common\service\system\DeptService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("部门管理")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("300")
 */
class Dept extends BaseController
{
    /**
     * @Apidoc\Title("部门列表")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned("list", ref="app\common\model\system\DeptModel", type="tree", desc="部门树形", field="dept_id,dept_pid,dept_name,dept_abbr,dept_desc,sort,is_disable,create_time,update_time")
     * @Apidoc\Returned("tree", ref="app\common\model\system\DeptModel", type="tree", desc="部门树形", field="dept_id,dept_pid,dept_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data['list']  = DeptService::list('tree', $where, []);
        $data['tree']  = DeptService::list('tree', [where_delete()], [], 'dept_id,dept_pid,dept_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("部门信息")
     * @Apidoc\Query(ref="app\common\model\system\DeptModel", field="dept_id")
     * @Apidoc\Returned(ref="app\common\model\system\DeptModel")
     */
    public function info()
    {
        $param['dept_id'] = $this->request->param('dept_id/d', 0);

        validate(DeptValidate::class)->scene('info')->check($param);

        $data = DeptService::info($param['dept_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("部门添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\DeptModel", field="dept_pid,dept_name,dept_abbr,dept_desc,dept_tel,dept_fax,dept_email,dept_addr,sort")
     */
    public function add()
    {
        $param['dept_pid']   = $this->request->param('dept_pid/d', 0);
        $param['dept_name']  = $this->request->param('dept_name/s', '');
        $param['dept_abbr']  = $this->request->param('dept_abbr/s', '');
        $param['dept_desc']  = $this->request->param('dept_desc/s', '');
        $param['dept_tel']   = $this->request->param('dept_tel/s', '');
        $param['dept_fax']   = $this->request->param('dept_fax/s', '');
        $param['dept_email'] = $this->request->param('dept_email/s', '');
        $param['dept_addr']  = $this->request->param('dept_addr/s', '');
        $param['sort']       = $this->request->param('sort/d', 250);

        validate(DeptValidate::class)->scene('add')->check($param);

        $data = DeptService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("部门修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\DeptModel", field="dept_id,dept_pid,dept_name,dept_abbr,dept_desc,dept_tel,dept_fax,dept_email,dept_addr,sort")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function edit()
    {
        $param['dept_id']    = $this->request->param('dept_id/d', 0);
        $param['dept_pid']   = $this->request->param('dept_pid/d', 0);
        $param['dept_name']  = $this->request->param('dept_name/s', '');
        $param['dept_abbr']  = $this->request->param('dept_abbr/s', '');
        $param['dept_desc']  = $this->request->param('dept_desc/s', '');
        $param['dept_tel']   = $this->request->param('dept_tel/s', '');
        $param['dept_fax']   = $this->request->param('dept_fax/s', '');
        $param['dept_email'] = $this->request->param('dept_email/s', '');
        $param['dept_addr']  = $this->request->param('dept_addr/s', '');
        $param['sort']       = $this->request->param('sort/d', 250);

        validate(DeptValidate::class)->scene('edit')->check($param);

        $data = DeptService::edit($param['dept_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("部门删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(DeptValidate::class)->scene('dele')->check($param);

        $data = DeptService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("部门修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\DeptModel", field="dept_pid")
     */
    public function editpid()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['dept_pid'] = $this->request->param('dept_pid/d', 0);

        validate(DeptValidate::class)->scene('editpid')->check($param);

        $data = DeptService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("部门是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\system\DeptModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(DeptValidate::class)->scene('disable')->check($param);

        $data = DeptService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("部门用户")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\system\DeptModel", field="dept_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\system\UserModel", type="array", desc="用户列表", field="user_id,nickname,username,sort,is_super,is_disable,create_time,update_time")
     */
    public function user()
    {
        $param['dept_id'] = $this->request->param('dept_id/d', 0);

        validate(DeptValidate::class)->scene('user')->check($param);

        $where = $this->where([where_delete(), ['dept_ids', 'in', [$param['dept_id']]]]);

        $data = DeptService::user($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("部门用户解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\DeptModel", field="dept_id")
     * @Apidoc\Param("user_ids", type="array", require=false, desc="用户id，为空则解除所有用户")
     */
    public function userRemove()
    {
        $param['dept_id']  = $this->request->param('dept_id/a', []);
        $param['user_ids'] = $this->request->param('user_ids/a', []);

        validate(DeptValidate::class)->scene('userRemove')->check($param);

        $data = DeptService::userRemove($param['dept_id'], $param['user_ids']);

        return success($data);
    }
}
