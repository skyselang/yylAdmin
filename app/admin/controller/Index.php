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
use app\common\service\cms\ContentService;
use app\common\service\file\FileService;
use app\common\service\MemberService;
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
        $msg  = 'yylAdmin后端安装成功，欢迎使用，如有帮助，敬请Star！';

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
        $date = Request::param('date/a', []);

        $data = MemberService::statDate($date);

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
}
