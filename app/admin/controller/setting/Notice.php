<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\setting\NoticeValidate;
use app\common\service\setting\NoticeService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("通告管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("200")
 */
class Notice extends BaseController
{
    /**
     * @Apidoc\Title("通告列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\NoticeModel", type="array", desc="通告列表", field="notice_id,type,title,title_color,start_time,end_time,is_disable,sort,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\setting\NoticeModel\getTypeNameAttr")
     * )
     * @Apidoc\Returned("types", type="array", desc="通告类型")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("通告信息")
     * @Apidoc\Param(ref="app\common\model\setting\NoticeModel", field="notice_id")
     * @Apidoc\Returned(ref="app\common\model\setting\NoticeModel")
     */
    public function info()
    {
        $param['notice_id'] = $this->request->param('notice_id/d', 0);

        validate(NoticeValidate::class)->scene('info')->check($param);

        $data = NoticeService::info($param['notice_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\NoticeModel", field="type,title,title_color,start_time,end_time,intro,content,sort")
     */
    public function add()
    {
        $param = $this->params(NoticeService::$edit_field);
        
        validate(NoticeValidate::class)->scene('add')->check($param);

        $data = NoticeService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\NoticeModel", field="notice_id,type,title,title_color,start_time,end_time,intro,content,sort")
     */
    public function edit()
    {
        $param = $this->params(NoticeService::$edit_field);

        validate(NoticeValidate::class)->scene('edit')->check($param);

        $data = NoticeService::edit($param['notice_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(NoticeValidate::class)->scene('dele')->check($param);

        $data = NoticeService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告修改类型")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\NoticeModel", field="type")
     */
    public function edittype()
    {
        $param['ids']  = $this->request->param('ids/a', []);
        $param['type'] = $this->request->param('type/d', 0);

        validate(NoticeValidate::class)->scene('edittype')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告时间范围")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\NoticeModel", field="start_time,end_time")
     */
    public function datetime()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['start_time'] = $this->request->param('start_time/s', '');
        $param['end_time']   = $this->request->param('end_time/s', '');

        validate(NoticeValidate::class)->scene('datetime')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("通告是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\NoticeModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(NoticeValidate::class)->scene('disable')->check($param);

        $data = NoticeService::edit($param['ids'], $param);

        return success($data);
    }
}
