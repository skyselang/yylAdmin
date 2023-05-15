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
use app\common\validate\member\LogValidate;
use app\common\service\member\LogService;
use app\common\service\member\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员日志")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("500")
 */
class Log extends BaseController
{
    /**
     * @Apidoc\Title("会员日志列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="日志列表", children={
     *   @Apidoc\Returned(ref="app\common\model\member\LogModel", field="log_id,member_id,api_id,request_ip,request_region,request_isp,response_code,response_msg,create_time"),
     *   @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="nickname,username"),
     *   @Apidoc\Returned(ref="app\common\model\member\ApiModel", field="api_url,api_name"),
     * })
     * @Apidoc\Returned("api", ref="app\common\model\member\ApiModel", type="tree", desc="接口树形", field="api_id,api_pid,api_name")
     * @Apidoc\Returned("log_types", type="array", desc="日志类型")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = LogService::list($where, $this->page(), $this->limit(), $this->order());

        $data['api']   = ApiService::list('tree', [where_delete()], [], 'api_id,api_pid,api_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志信息")
     * @Apidoc\Query(ref="app\common\model\member\LogModel", field="log_id")
     * @Apidoc\Returned(ref="app\common\model\member\LogModel")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="nickname,username")
     * @Apidoc\Returned(ref="app\common\model\member\ApiModel", field="api_url,api_name")
     */
    public function info()
    {
        $param = $this->params(['log_id/d' => 0]);

        validate(LogValidate::class)->scene('info')->check($param);

        $data = LogService::info($param['log_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(LogValidate::class)->scene('dele')->check($param);

        $data = LogService::dele($param['ids'], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志清空")
     * @Apidoc\Method("POST")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     */
    public function clear()
    {
        $where = $this->where();

        $data = LogService::clear($where);

        return success($data);
    }
}
