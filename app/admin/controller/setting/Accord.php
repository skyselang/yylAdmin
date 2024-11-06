<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\setting\AccordValidate;
use app\common\service\setting\AccordService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("协议管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("300")
 */
class Accord extends BaseController
{
    /**
     * @Apidoc\Title("协议列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\AccordModel", type="array", desc="协议列表", field="accord_id,unique,name,desc,remark,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = AccordService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();

        return success($data);
    }

    /**
     * @Apidoc\Title("协议信息")
     * @Apidoc\Query(ref="app\common\model\setting\AccordModel", field="accord_id")
     * @Apidoc\Returned(ref="app\common\model\setting\AccordModel")
     */
    public function info()
    {
        $param = $this->params(['accord_id/d' => '']);

        validate(AccordValidate::class)->scene('info')->check($param);

        $data = AccordService::info($param['accord_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("协议添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\AccordModel", field="unique,name,desc,content,remark,sort")
     */
    public function add()
    {
        $param = $this->params(AccordService::$edit_field);

        validate(AccordValidate::class)->scene('add')->check($param);

        $data = AccordService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("协议修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\AccordModel", field="accord_id,unique,name,desc,content,remark,sort")
     */
    public function edit()
    {
        $param = $this->params(AccordService::$edit_field);

        validate(AccordValidate::class)->scene('edit')->check($param);

        $data = AccordService::edit($param['accord_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("协议删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(AccordValidate::class)->scene('dele')->check($param);

        $data = AccordService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("协议是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\AccordModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(AccordValidate::class)->scene('disable')->check($param);

        $data = AccordService::edit($param['ids'], $param);

        return success($data);
    }
}
