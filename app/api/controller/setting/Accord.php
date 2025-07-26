<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\setting\AccordValidate;
use app\common\service\setting\AccordService;

/**
 * @Apidoc\Title("lang(协议)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("400")
 */
class Accord extends BaseController
{
    /**
     * @Apidoc\Title("lang(协议列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query("unique", type="string", default="", desc="编号，多个逗号隔开")
     * @Apidoc\Query(ref={AccordService::class,"edit"}, field="name")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref={AccordService::class,"info"}, type="array", desc="协议列表", field="accord_id,unique,name,desc,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $unique = $this->param('unique/s', '');
        $name   = $this->param('name/s', '');

        $where = [['accord_id', '>', 0]];
        if ($unique) {
            $where[] = ['unique', 'in', $unique];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        $where = where_disdel($where);

        $order = ['sort' => 'desc', 'accord_id' => 'desc'];

        $field = 'unique,name,desc,sort,is_disable,create_time,update_time';

        $data = AccordService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(协议信息)")
     * @Apidoc\Query("accord_id", type="string", require=true, default="", desc="协议id、编号")
     * @Apidoc\Returned(ref={AccordService::class,"info"})
     */
    public function info()
    {
        $param = $this->params(['accord_id/s' => '']);

        validate(AccordValidate::class)->scene('info')->check($param);

        $data = AccordService::info($param['accord_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error(lang('协议不存在'));
        }

        return success($data);
    }
}
