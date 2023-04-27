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
use app\common\validate\setting\NoticeValidate;
use app\common\service\setting\NoticeService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("通告")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("200")
 */
class Notice extends BaseController
{
    /**
     * @Apidoc\Title("通告列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\NoticeModel", type="array", desc="通告列表", field="notice_id,type,title,title_color,intro,start_time,end_time,sort",
     *   @Apidoc\Returned(ref="app\common\model\setting\NoticeModel\getImageUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\NoticeModel\getTypeNameAttr")
     * )
     * @Apidoc\Returned("types", type="array", desc="通告类型")
     */
    public function list()
    {
        $where[] = ['start_time', '<=', datetime()];
        $where[] = ['end_time', '>=', datetime()];
        $where[] = where_disable();
        $where[] = where_delete();
        $where = $this->where($where);

        $order = ['sort' => 'desc', 'start_time' => 'desc', 'notice_id' => 'desc'];

        $field = 'notice_id,image_id,type,title,title_color,intro,start_time,end_time,sort';

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告信息")
     * @Apidoc\Query(ref="app\common\model\setting\NoticeModel", field="notice_id")
     * @Apidoc\Returned(ref="app\common\model\setting\NoticeModel",
     *   @Apidoc\Returned(ref="app\common\model\setting\NoticeModel\getImageUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\NoticeModel\getTypeNameAttr")
     * )
     */
    public function info()
    {
        $param['notice_id'] = $this->request->param('notice_id/d', 0);

        validate(NoticeValidate::class)->scene('info')->check($param);

        $data = NoticeService::info($param['notice_id']);

        return success($data);
    }
}
