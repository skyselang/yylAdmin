<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\setting\AccordValidate;
use app\common\service\setting\AccordService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("协议")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("800")
 */
class Accord extends BaseController
{
    /**
     * @Apidoc\Title("协议列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query("name", type="string", default="", desc="名称")
     * @Apidoc\Query("unique", type="string", default="", desc="标识，多个逗号隔开")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\AccordModel", type="array", desc="协议列表", field="accord_id,unique,name,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $name   = $this->param('name/s', '');
        $unique = $this->param('unique/s', '');

        $where[] = ['accord_id', '>', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($unique) {
            $where[] = ['unique', 'in', $unique];
        }
        $where[] = where_disable();
        $where[] = where_delete();

        $order = ['sort' => 'desc', 'accord_id' => 'desc'];

        $data = AccordService::list($where, $this->page(), $this->limit(), $this->order($order));

        return success($data);
    }

    /**
     * @Apidoc\Title("协议信息")
     * @Apidoc\Query("accord_id", type="string", require=true, default="", desc="协议id、标识")
     * @Apidoc\Returned(ref="app\common\model\setting\AccordModel")
     */
    public function info()
    {
        $param = $this->params(['accord_id/s' => '']);

        validate(AccordValidate::class)->scene('info')->check($param);

        $data = AccordService::info($param['accord_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error([], '协议不存在');
        }

        return success($data);
    }
}
