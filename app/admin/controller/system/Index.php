<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\service\system\IndexService;
use app\common\service\system\NoticeService;
use app\common\service\member\MemberService;
use app\common\service\content\ContentService;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("控制台")
 * @Apidoc\Group("console")
 * @Apidoc\Sort("150")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("首页")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = '后端安装成功，欢迎使用，如有帮助，敬请Star！';

        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("公告")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="公告列表", children={
     *   @Apidoc\Returned(ref="app\common\model\system\NoticeModel", field="notice_id,image_id,title,title_color,intro,start_time"),
     *   @Apidoc\Returned(ref="app\common\model\system\NoticeModel\getImageUrlAttr", field="image_url")
     * })
     */
    public function notice()
    {
        $where[] = ['start_time', '<=', datetime()];
        $where[] = ['end_time', '>=', datetime()];
        $where[] = where_disable();
        $where[] = where_delete();
        $where = $this->where($where);

        $order = ['sort' => 'desc', 'start_time' => 'desc', 'notice_id' => 'desc'];

        $field = 'notice_id,image_id,title,title_color,intro,start_time';

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("总数统计")
     * @Apidoc\Returned(ref="app\common\service\system\IndexService\count")
     */
    public function count()
    {
        $data = IndexService::count();

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Query("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Query("date", type="array", default="", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("number", type="array", desc="图表数据", children={
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
     * @Apidoc\Title("内容统计")
     * @Apidoc\Returned(ref="app\common\service\content\ContentService\statistic")
     */
    public function content()
    {
        $data = ContentService::statistic();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件统计")
     * @Apidoc\Returned(ref="app\common\service\file\FileService\statistic")
     */
    public function file()
    {
        $data = FileService::statistic();

        return success($data);
    }
}
