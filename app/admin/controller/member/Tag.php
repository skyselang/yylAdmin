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
use app\common\validate\member\TagValidate;
use app\common\service\member\TagService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员标签")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("200")
 */
class Tag extends BaseController
{
    /**
     * @Apidoc\Title("会员标签列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\TagModel", type="array", desc="标签列表", field="tag_id,tag_name,remark,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = TagService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签信息")
     * @Apidoc\Query(ref="app\common\model\member\TagModel", field="tag_id")
     * @Apidoc\Returned(ref="app\common\model\member\TagModel")
     */
    public function info()
    {
        $param = $this->params(['tag_id/d' => '']);

        validate(TagValidate::class)->scene('info')->check($param);

        $data = TagService::info($param['tag_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\TagModel", field="tag_name,tag_desc,remark,sort")
     */
    public function add()
    {
        $param = $this->params(TagService::$edit_field);

        validate(TagValidate::class)->scene('add')->check($param);

        $data = TagService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\TagModel", field="tag_id,tag_name,tag_desc,remark,sort")
     */
    public function edit()
    {
        $param = $this->params(TagService::$edit_field);

        validate(TagValidate::class)->scene('edit')->check($param);

        $data = TagService::edit($param['tag_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(TagValidate::class)->scene('dele')->check($param);

        $data = TagService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\TagModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(TagValidate::class)->scene('disable')->check($param);

        $data = TagService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签会员列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\member\TagModel", field="tag_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="会员列表", children={
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,avatar_id,nickname,username,phone,email,sort,is_super,is_disable,create_time"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getTagNamesAttr", field="tag_names"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel\getGroupNamesAttr", field="group_names"),
     * })
     */
    public function member()
    {
        $param = $this->params(['tag_id/d' => '']);

        validate(TagValidate::class)->scene('member')->check($param);

        $where = $this->where(where_delete(['tag_ids', 'in', [$param['tag_id']]]));

        $data = TagService::member($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("会员标签会员解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("tag_id", type="array", require=true, desc="标签id")
     * @Apidoc\Param("member_ids", type="array", require=false, desc="会员id，为空则解除所有会员")
     */
    public function memberRemove()
    {
        $param = $this->params(['tag_id/a' => [], 'member_ids/a' => []]);

        validate(TagValidate::class)->scene('memberRemove')->check($param);

        $data = TagService::memberRemove($param['tag_id'], $param['member_ids']);

        return success($data);
    }
}
