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
use app\common\validate\member\ThirdValidate;
use app\common\service\member\ThirdService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员第三方账号")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("600")
 */
class Third extends BaseController
{
    /**
     * @Apidoc\Title("会员第三方账号列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\ThirdModel", type="array", desc="第三方账号列表", field="third_id,platform,application,openid,is_disable,create_time,update_time")
     * @Apidoc\Returned("platforms", type="array", desc="平台")
     * @Apidoc\Returned("applications", type="array", desc="应用")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = ThirdService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();

        return success($data);
    }

    /**
     * @Apidoc\Title("会员第三方账号信息")
     * @Apidoc\Query(ref="app\common\model\member\ThirdModel", field="third_id")
     * @Apidoc\Returned(ref="app\common\model\member\ThirdModel")
     */
    public function info()
    {
        $param = $this->params(['third_id/d' => '']);

        validate(ThirdValidate::class)->scene('info')->check($param);

        $data = ThirdService::info($param['third_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员第三方账号添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\ThirdModel", field="member_id,platform,application,unionid,openid,headimgurl,nickname,remark")
     */
    public function add()
    {
        $param = $this->params(ThirdService::$edit_field);

        validate(ThirdValidate::class)->scene('add')->check($param);

        $data = ThirdService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员第三方账号修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\ThirdModel", field="third_id,member_id,platform,application,unionid,openid,headimgurl,nickname,remark")
     */
    public function edit()
    {
        $param = $this->params(ThirdService::$edit_field);

        validate(ThirdValidate::class)->scene('edit')->check($param);

        $data = ThirdService::edit($param['third_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员第三方账号删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ThirdValidate::class)->scene('dele')->check($param);

        $data = ThirdService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员第三方账号是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\ThirdModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(ThirdValidate::class)->scene('disable')->check($param);

        $data = ThirdService::edit($param['ids'], $param);

        return success($data);
    }
}
