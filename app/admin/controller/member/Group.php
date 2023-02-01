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
use app\common\validate\member\GroupValidate;
use app\common\service\member\GroupService;
use app\common\service\member\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员分组")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Group extends BaseController
{
    /**
     * @Apidoc\Title("会员分组列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\GroupModel", type="array", desc="分组列表", field="group_id,group_name,group_desc,sort,is_default,is_disable,create_time,update_time")
     * @Apidoc\Returned("api", ref="app\common\model\member\ApiModel", type="tree", desc="接口树形", field="api_id,api_pid,api_name,api_url,is_unlogin,is_unauth")
     * @Apidoc\Returned("api_ids", type="array", desc="接口id")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = GroupService::list($where, $this->page(), $this->limit(), $this->order());
        $api  = ApiService::list('list', [where_delete()], [], 'api_id,api_pid,api_name,api_url,is_unlogin,is_unauth');

        $data['api']     = list_to_tree($api, 'api_id', 'api_pid');
        $data['api_ids'] = array_column($api, 'api_id');
        $data['exps']    = where_exps();
        $data['where']   = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组信息")
     * @Apidoc\Query(ref="app\common\model\member\GroupModel", field="group_id")
     * @Apidoc\Returned(ref="app\common\model\member\GroupModel")
     */
    public function info()
    {
        $param['group_id'] = $this->request->param('group_id/d', 0);

        validate(GroupValidate::class)->scene('info')->check($param);

        $data = GroupService::info($param['group_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="group_name,group_desc,sort")
     * @Apidoc\Param("api_ids", type="array", desc="接口id")
     */
    public function add()
    {
        $param = $this->params(GroupService::$edit_field);
        
        validate(GroupValidate::class)->scene('add')->check($param);

        $data = GroupService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="group_id,group_name,group_desc,sort")
     * @Apidoc\Param("api_ids", type="array", desc="接口id")
     */
    public function edit()
    {
        $param = $this->params(GroupService::$edit_field);

        validate(GroupValidate::class)->scene('edit')->check($param);

        $data = GroupService::edit($param['group_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(GroupValidate::class)->scene('dele')->check($param);

        $data = GroupService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组修改接口")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param("api_ids", type="array", desc="接口id")
     */
    public function editapi()
    {
        $param['ids']     = $this->request->param('ids/a', []);
        $param['api_ids'] = $this->request->param('api_ids/a', []);

        validate(GroupValidate::class)->scene('editapi')->check($param);

        $data = GroupService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组是否默认")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="is_default")
     */
    public function defaults()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_default'] = $this->request->param('is_default/d', 0);

        validate(GroupValidate::class)->scene('default')->check($param);

        $data = GroupService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(GroupValidate::class)->scene('disable')->check($param);

        $data = GroupService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组会员")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\member\GroupModel", field="group_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\MemberModel", type="array", desc="会员列表", field="member_id,avatar_id,nickname,username,phone,email,sort,is_super,is_disable,create_time",
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getTagNamesAttr"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getGroupNamesAttr"),
     * )
     */
    public function member()
    {
        $group_id = $this->request->param('group_id/d', 0);

        validate(GroupValidate::class)->scene('member')->check(['group_id' => $group_id]);

        $where = $this->where(where_delete(['group_ids', 'in', [$group_id]]));

        $data = GroupService::member($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组会员解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="group_id")
     * @Apidoc\Param("member_ids", type="array", require=false, desc="会员id，为空则解除所有会员")
     */
    public function memberRemove()
    {
        $param['group_id']   = $this->request->param('group_id/a', []);
        $param['member_ids'] = $this->request->param('member_ids/a', []);

        validate(GroupValidate::class)->scene('memberRemove')->check($param);

        $data = GroupService::memberRemove($param['group_id'], $param['member_ids']);

        return success($data);
    }
}
