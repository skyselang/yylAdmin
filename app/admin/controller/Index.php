<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 控制台控制器
namespace app\admin\controller;

use think\facade\Request;
use app\common\service\IndexService;
use app\common\service\member\MemberService;
use app\common\service\cms\ContentService;
use app\common\service\file\FileService;
use app\common\service\admin\NoticeService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("控制台")
 * @Apidoc\Group("adminConsole")
 * @Apidoc\Sort("150")
 */
class Index
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
     * @Apidoc\Title("总数统计")
     */
    public function count()
    {
        $data = IndexService::count();

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     */
    public function member()
    {
        $type = Request::param('type/s', '');
        $date = Request::param('date/a', []);

        $data['number'] = MemberService::stat($type, $date, 'number');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容统计")
     */
    public function cms()
    {
        $data = ContentService::statistics();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件统计")
     */
    public function file()
    {
        $data = FileService::statistics();

        return success($data);
    }

    /**
     * @Apidoc\Title("公告")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="公告列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\NoticeModel\listReturn")
     * )
     */
    public function notice()
    {
        $page  = Request::param('page/d', 1);
        $limit = Request::param('limit/d', 10);

        $where[] = ['open_time_start', '<=', datetime()];
        $where[] = ['open_time_end', '>=', datetime()];
        $where[] = ['is_open', '=', 1];
        $where[] = ['is_delete', '=', 0];

        $order = ['sort' => 'desc', 'open_time_start' => 'desc'];

        $field = 'admin_notice_id,admin_user_id,title,color,intro,create_time';

        $data = NoticeService::list($where, $page, $limit, $order, $field);

        return success($data);
    }
}
