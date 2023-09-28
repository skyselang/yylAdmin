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
use app\common\validate\setting\LinkValidate;
use app\common\service\setting\LinkService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("友链管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("550")
 */
class Link extends BaseController
{
    /**
     * @Apidoc\Title("友链列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="友链列表", children={
     *   @Apidoc\Returned(ref="app\common\model\setting\LinkModel", field="link_id,unique,image_id,name,name_color,url,desc,sort,is_disable,start_time,end_time,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\setting\LinkModel\getImageUrlAttr", field="image_url")
     * })
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = LinkService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("友链信息")
     * @Apidoc\Query(ref="app\common\model\setting\LinkModel", field="link_id")
     * @Apidoc\Returned(ref="app\common\model\setting\LinkModel")
     */
    public function info()
    {
        $param = $this->params(['link_id/d' => '']);

        validate(LinkValidate::class)->scene('info')->check($param);

        $data = LinkService::info($param['link_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\LinkModel", field="unique,image_id,name,name_color,url,desc,start_time,end_time,underline,remark,sort")
     */
    public function add()
    {
        $param = $this->params(LinkService::$edit_field);

        validate(LinkValidate::class)->scene('add')->check($param);

        $data = LinkService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\LinkModel", field="link_id,unique,image_id,name,name_color,url,desc,start_time,end_time,underline,remark,sort")
     */
    public function edit()
    {
        $param = $this->params(LinkService::$edit_field);

        validate(LinkValidate::class)->scene('edit')->check($param);

        $data = LinkService::edit($param['link_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(LinkValidate::class)->scene('dele')->check($param);

        $data = LinkService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\LinkModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(LinkValidate::class)->scene('disable')->check($param);

        $data = LinkService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("友链修改时间")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\LinkModel", field="start_time,end_time")
     */
    public function datetime()
    {
        $param = $this->params(['ids/a' => [], 'start_time/s' => '', 'end_time/s' => '']);

        validate(LinkValidate::class)->scene('datetime')->check($param);

        $data = LinkService::edit($param['ids'], $param);

        return success($data);
    }
}
