<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\service\system\IndexService;
use app\common\service\system\NoticeService;
use app\common\service\member\MemberService;
use app\common\service\content\ContentService;
use app\common\service\file\FileService;

/**
 * @Apidoc\Title("lang(首页)")
 * @Apidoc\Group("home")
 * @Apidoc\Sort("150")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("lang(首页)")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = lang('后端安装成功，欢迎使用，如有帮助，敬请Star！');

        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("lang(公告)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned(ref={NoticeService::class,"list"})
     */
    public function notice()
    {
        $where = $this->where(where_disdel([['start_time', '<=', datetime()], ['end_time', '>=', datetime()]]));

        $order = ['sort' => 'desc', 'start_time' => 'desc', 'notice_id' => 'desc'];

        $field = 'image_id,title,title_color,start_time,end_time';

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(总数统计)")
     * @Apidoc\Returned(ref="app\common\service\system\IndexService\count")
     */
    public function count()
    {
        $data = IndexService::count();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员统计)")
     * @Apidoc\Query(ref="app\common\service\member\MemberService\statistic")
     * @Apidoc\Returned("number", type="array", desc="lang(图表数据)", children={
     *   @Apidoc\Returned(ref="app\common\service\member\MemberService\statistic")
     * })
     */
    public function member()
    {
        $type = $this->param('type/s', '');
        $date = $this->param('date/a', []);

        $data['number'] = MemberService::statistic($type, $date, 'number');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(内容统计)")
     * @Apidoc\Returned(ref="app\common\service\content\ContentService\statistic")
     */
    public function content()
    {
        $data = ContentService::statistic();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件统计)")
     * @Apidoc\Returned(ref="app\common\service\file\FileService\statistic")
     */
    public function file()
    {
        $data = FileService::statistic();

        return success($data);
    }
}
