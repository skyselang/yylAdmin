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
use app\common\validate\setting\NoticeValidate;
use app\common\service\setting\NoticeService;

/**
 * @Apidoc\Title("lang(通告)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("300")
 */
class Notice extends BaseController
{
    /**
     * @Apidoc\Title("lang(通告列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query("unique", type="string", default="", desc="编号，多个逗号隔开")
     * @Apidoc\Query(ref={NoticeService::class,"edit"}, field="type,title")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned(ref={NoticeService::class,"list"})
     */
    public function list()
    {
        $unique = $this->param('unique/s', '');
        $type   = $this->param('type/s', '');
        $title  = $this->param('title/s', '');

        $where = [['notice_id', '>', 0]];
        if ($unique) {
            $where[] = ['unique', 'in', $unique];
        }
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        $where[] = ['start_time', '<=', datetime()];
        $where[] = ['end_time', '>=', datetime()];
        $where = where_disdel($where);

        $order = ['sort' => 'desc', 'start_time' => 'desc', 'notice_id' => 'desc'];

        $field = 'unique,image_id,type,title,title_color,desc,start_time,end_time,sort';

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(通告信息)")
     * @Apidoc\Query(ref={NoticeService::class,"info"})
     * @Apidoc\Returned(ref={NoticeService::class,"info"})
     */
    public function info()
    {
        $param = $this->params(['notice_id' => '']);

        validate(NoticeValidate::class)->scene('info')->check($param);

        $data = NoticeService::info($param['notice_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error(lang('通告不存在'));
        }

        return success($data);
    }
}
