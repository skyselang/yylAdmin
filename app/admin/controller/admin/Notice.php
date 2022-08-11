<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\NoticeValidate;
use app\common\service\admin\NoticeService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("公告管理")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("735")
 */
class Notice extends BaseController
{
    /**
     * @Apidoc\Title("公告列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\admin\NoticeModel\listReturn", type="array", desc="公告列表")
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'admin_notice_id,admin_user_id,username,is_open', true);

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("公告信息")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\NoticeModel\infoReturn")
     */
    public function info()
    {
        $param['admin_notice_id'] = $this->param('admin_notice_id/d', '');

        validate(NoticeValidate::class)->scene('info')->check($param);

        $data = NoticeService::info($param['admin_notice_id']);
        if ($data['is_delete'] == 1) {
            exception('公告已被删除：' . $param['admin_notice_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("公告添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\addParam")
     * @Apidoc\Param("title", mock="@ctitle(15, 32)")
     * @Apidoc\Param("open_time_start", mock="@now")
     * @Apidoc\Param("open_time_end", mock="@now")
     * @Apidoc\Param("intro", mock="@csentence(32, 64)")
     * @Apidoc\Param("content", mock="@cparagraph(64, 128)")
     */
    public function add()
    {
        $param['admin_user_id']   = admin_user_id();
        $param['title']           = $this->param('title/s', '');
        $param['color']           = $this->param('color/s', '#606266');
        $param['type']            = $this->param('type/d', 1);
        $param['sort']            = $this->param('sort/d', 250);
        $param['open_time_start'] = $this->param('open_time_start/s', '');
        $param['open_time_end']   = $this->param('open_time_end/s', '');
        $param['intro']           = $this->param('intro/s', '');
        $param['content']         = $this->param('content/s', '');

        validate(NoticeValidate::class)->scene('add')->check($param);

        $data = NoticeService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\editParam")
     */
    public function edit()
    {
        $param['admin_notice_id'] = $this->param('admin_notice_id/d', '');
        $param['title']           = $this->param('title/s', '');
        $param['color']           = $this->param('color/s', '#606266');
        $param['type']            = $this->param('type/d', 1);
        $param['sort']            = $this->param('sort/d', 250);
        $param['open_time_start'] = $this->param('open_time_start/s', '');
        $param['open_time_end']   = $this->param('open_time_end/s', '');
        $param['intro']           = $this->param('intro/s', '');
        $param['content']         = $this->param('content/s', '');

        validate(NoticeValidate::class)->scene('edit')->check($param);

        $data = NoticeService::edit($param['admin_notice_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(NoticeValidate::class)->scene('dele')->check($param);

        $data = NoticeService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告是否开启")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\is_open")
     */
    public function isopen()
    {
        $param['ids']     = $this->param('ids/a', '');
        $param['is_open'] = $this->param('is_open/d', 0);

        validate(NoticeValidate::class)->scene('isopen')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("公告开启时间")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\open_time_start")
     * @Apidoc\Param(ref="app\common\model\admin\NoticeModel\open_time_end")
     */
    public function opentime()
    {
        $param['ids']             = $this->param('ids/a', '');
        $param['open_time_start'] = $this->param('open_time_start/s', '');
        $param['open_time_end']   = $this->param('open_time_end/s', '');

        validate(NoticeValidate::class)->scene('opentime')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }
}
