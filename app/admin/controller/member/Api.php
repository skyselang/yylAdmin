<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use app\common\controller\BaseController;
use app\common\validate\member\ApiValidate;
use app\common\service\member\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员接口")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("400")
 */
class Api extends BaseController
{
    /**
     * @Apidoc\Title("会员接口列表")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\ApiModel", type="tree", desc="接口树形", field="api_id,api_pid,api_name,api_url,sort,is_unlogin,is_unauth,is_unrate,is_disable")
     * @Apidoc\Returned("tree", ref="app\common\model\member\ApiModel", type="tree", desc="接口树形", field="api_id,api_pid,api_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data['list']  = ApiService::list('tree', $where, $this->order());
        $data['tree']  = ApiService::list('tree', [where_delete()], [], 'api_id,api_pid,api_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口信息")
     * @Apidoc\Query(ref="app\common\model\member\ApiModel", field="api_id")
     * @Apidoc\Returned(ref="app\common\model\member\ApiModel")
     */
    public function info()
    {
        $param['api_id'] = $this->request->param('api_id/d', 0);

        validate(ApiValidate::class)->scene('info')->check($param);

        $data = ApiService::info($param['api_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="api_pid,api_name,api_url,sort")
     */
    public function add()
    {
        $param = $this->params(ApiService::$edit_field);
        
        validate(ApiValidate::class)->scene('add')->check($param);

        $data = ApiService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="api_id,api_pid,api_name,api_url,sort")
     */
    public function edit()
    {
        $param = $this->params(ApiService::$edit_field);

        validate(ApiValidate::class)->scene('edit')->check($param);

        $data = ApiService::edit($param['api_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(ApiValidate::class)->scene('dele')->check($param);

        $data = ApiService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口修改排序")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="sort")
     */
    public function editsort()
    {
        $param['ids']         = $this->request->param('ids/a', []);
        $param['sort']        = $this->request->param('sort/d', 250);
        $param['sort_incdec'] = $this->request->param('sort_incdec/d', 0);

        validate(ApiValidate::class)->scene('editsort')->check($param);

        if ($param['sort_incdec']) {
            foreach ($param['ids'] as $k => $id) {
                $data[] = ApiService::edit([$id], ['sort' => $param['sort_incdec'] * $k + $param['sort']]);
            }
        } else {
            $data = ApiService::edit($param['ids'], $param);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="api_pid")
     */
    public function editpid()
    {
        $param['ids']     = $this->request->param('ids/a', []);
        $param['api_pid'] = $this->request->param('api_pid/d', 0);

        validate(ApiValidate::class)->scene('editpid')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口是否免登")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="is_unlogin")
     */
    public function unlogin()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_unlogin'] = $this->request->param('is_unlogin/d', 0);

        validate(ApiValidate::class)->scene('unlogin')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口是否免权")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="is_unauth")
     */
    public function unauth()
    {
        $param['ids']       = $this->request->param('ids/a', []);
        $param['is_unauth'] = $this->request->param('is_unauth/d', 0);

        validate(ApiValidate::class)->scene('unauth')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口是否免限")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="is_unrate")
     */
    public function unrate()
    {
        $param['ids']       = $this->request->param('ids/a', []);
        $param['is_unrate'] = $this->request->param('is_unrate/d', 0);

        validate(ApiValidate::class)->scene('unrate')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(ApiValidate::class)->scene('disable')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口分组")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\member\ApiModel", field="api_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\GroupModel", type="array", desc="分组列表", field="group_id,group_name,group_desc,sort,is_default,is_disable,create_time,update_time")
     */
    public function group()
    {
        $param['api_id'] = $this->request->param('api_id/d', 0);

        validate(ApiValidate::class)->scene('group')->check($param);

        $where = $this->where(where_delete(['api_ids', 'in', [$param['api_id']]]));

        $data = ApiService::group($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("会员接口分组解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\ApiModel", field="api_id")
     * @Apidoc\Param("group_ids", type="array", require=false, desc="分组id，为空则解除所有接口")
     */
    public function groupRemove()
    {
        $param['api_id']    = $this->request->param('api_id/a', []);
        $param['group_ids'] = $this->request->param('group_ids/a', []);

        validate(ApiValidate::class)->scene('groupRemove')->check($param);

        $data = ApiService::groupRemove($param['api_id'], $param['group_ids']);

        return success($data);
    }
}
