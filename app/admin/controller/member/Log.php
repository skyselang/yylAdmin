<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use app\common\BaseController;
use app\common\validate\member\LogValidate;
use app\common\service\member\LogService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员日志")
 * @Apidoc\Group("adminMember")
 * @Apidoc\Sort("220")
 */
class Log extends BaseController
{
    /**
     * @Apidoc\Title("会员日志列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\member\LogModel\log_type")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\LogModel\listReturn", type="array", desc="会员日志列表")
     */
    public function list()
    {
        $log_type = $this->param('log_type/d', '');
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        $where[] = ['is_delete', '=', 0];
        $where = $this->where($where, 'member_id,username,api_id,api_url,api_name');

        $data = LogService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志信息")
     * @Apidoc\Param(ref="app\common\model\member\LogModel\id")
     * @Apidoc\Returned(ref="app\common\model\member\LogModel\infoReturn")
     */
    public function info()
    {
        $param['member_log_id'] = $this->param('member_log_id/d', '');

        validate(LogValidate::class)->scene('info')->check($param);

        $data = LogService::info($param['member_log_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(LogValidate::class)->scene('dele')->check($param);

        $data = LogService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\id")
     * @Apidoc\Param("member_id", require=false, default="")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\username")
     * @Apidoc\Param("username", require=false, default="")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\id")
     * @Apidoc\Param("api_id", require=false, default="")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\api_url")
     * @Apidoc\Param("api_url", require=false, default="")
     * @Apidoc\Param(ref="dateParam")
     */
    public function clear()
    {
        $member_id  = $this->param('member_id/s', '');
        $username   = $this->param('username/s', '');
        $api_id     = $this->param('api_id/s', '');
        $api_url    = $this->param('api_url/s', '');
        $date_value = $this->param('date_value/a', '');

        $where = [];
        if ($member_id) {
            $exp = strpos($member_id, ',') ? 'in' : '=';
            $where[] = ['member_id', $exp, $member_id];
        }
        if ($username) {
            $exp = strpos($username, ',') ? 'in' : '=';
            $where[] = ['username', $exp, $username];
        }
        if ($api_id) {
            $exp = strpos($api_id, ',') ? 'in' : '=';
            $where[] = ['api_id', $exp, $api_id];
        }
        if ($api_url) {
            $exp = strpos($api_url, ',') ? 'in' : '=';
            $where[] = ['api_url', $exp, $api_url];
        }
        if ($date_value) {
            $where[] = ['create_time', '>=', $date_value[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = LogService::clear($where);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志清空")
     * @Apidoc\Method("POST")
     */
    public function clean()
    {
        $data = LogService::clear([], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志统计")
     * @Apidoc\Param("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Param("date", type="array", default="[]", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("count", type="object", desc="数量统计",
     *     @Apidoc\Returned("name", type="string", desc="名称"),
     *     @Apidoc\Returned("date", type="string", desc="时间"),
     *     @Apidoc\Returned("count", type="string", desc="数量"),
     *     @Apidoc\Returned("title", type="string", desc="title")
     * )
     * @Apidoc\Returned("echart", type="array", desc="图表数据",
     *     @Apidoc\Returned("type", type="string", desc="日期类型"),
     *     @Apidoc\Returned("date", type="array", desc="日期范围"),
     *     @Apidoc\Returned("title", type="string", desc="图表title.text"),
     *     @Apidoc\Returned("legend", type="array", desc="图表legend.data"),
     *     @Apidoc\Returned("xAxis", type="string", desc="图表xAxis.data"),
     *     @Apidoc\Returned("series", type="string", desc="图表series")
     * )
     */
    public function stat()
    {
        $type  = $this->param('type/s', 'day');
        $date  = $this->param('date/a', []);
        $field = $this->param('field/s', 'request_province');

        $data['count'] = LogService::stat($type, $date, 'count');
        $data['echart'][] = LogService::stat($type, $date, 'number');
        $data['field'] = LogService::statField($type, $date, $field);

        return success($data);
    }
}
