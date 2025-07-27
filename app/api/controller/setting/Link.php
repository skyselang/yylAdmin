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
use app\common\validate\setting\LinkValidate;
use app\common\service\setting\LinkService;

/**
 * @Apidoc\Title("lang(友链)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("500")
 */
class Link extends BaseController
{
    /**
     * @Apidoc\Title("lang(友链列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query("unique", type="string", default="", desc="编号，多个逗号隔开")
     * @Apidoc\Query(ref={LinkService::class,"edit"}, field="name")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned(ref={LinkService::class,"list"})
     */
    public function list()
    {
        $unique = $this->param('unique/s', '');
        $name   = $this->param('name/s', '');

        $where = [['link_id', '>', 0]];
        if ($unique) {
            $where[] = ['unique', '=', $unique];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        $where[] = ['start_time', '<=', datetime()];
        $where[] = ['end_time', '>=', datetime()];
        $where = where_disdel($where);

        $order = ['sort' => 'desc', 'start_time' => 'desc', 'link_id' => 'desc'];

        $field = 'unique,image_id,name,name_color,url,desc';

        $data = LinkService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(友链信息)")
     * @Apidoc\Query(ref={LinkService::class,"info"})
     * @Apidoc\Returned(ref={LinkService::class,"info"})
     */
    public function info()
    {
        $param = $this->params(['link_id' => '']);

        validate(LinkValidate::class)->scene('info')->check($param);

        $data = LinkService::info($param['link_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error(lang('友链不存在'));
        }

        return success($data);
    }
}
