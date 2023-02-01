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
use app\common\validate\setting\CarouselValidate;
use app\common\service\setting\CarouselService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("轮播管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("100")
 */
class Carousel extends BaseController
{
    /**
     * @Apidoc\Title("轮播列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\CarouselModel", type="array", desc="轮播列表", field="carousel_id,file_id,file_type,title,link,position,sort,is_disable,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\setting\CarouselModel\getFileUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\CarouselModel\getFileTypeNameAttr")
     * )
     * @Apidoc\Returned("filetypes", type="array", desc="文件类型")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = CarouselService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播信息")
     * @Apidoc\Query(ref="app\common\model\setting\CarouselModel", field="carousel_id")
     * @Apidoc\Returned(ref="app\common\model\setting\CarouselModel",
     *   @Apidoc\Returned(ref="app\common\model\setting\CarouselModel\getFileUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\CarouselModel\getFileExtAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\CarouselModel\getFileNameAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\CarouselModel\getFileTypeNameAttr")
     * )
     */
    public function info()
    {
        $param['carousel_id'] = $this->request->param('carousel_id/d', 0);

        validate(CarouselValidate::class)->scene('info')->check($param);

        $data = CarouselService::info($param['carousel_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\CarouselModel", field="file_id,file_type,title,link,position,sort")
     */
    public function add()
    {
        $param = $this->params(CarouselService::$edit_field);

        validate(CarouselValidate::class)->scene('add')->check($param);

        $data = CarouselService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\CarouselModel", field="carousel_id,file_id,file_type,title,link,position,sort")
     */
    public function edit()
    {
        $param = $this->params(CarouselService::$edit_field);

        validate(CarouselValidate::class)->scene('edit')->check($param);

        $data = CarouselService::edit($param['carousel_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(CarouselValidate::class)->scene('dele')->check($param);

        $data = CarouselService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播修改位置")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\CarouselModel", field="position")
     */
    public function position()
    {
        $param['ids']      = $this->request->param('ids/a', []);
        $param['position'] = $this->request->param('position/s', '');

        validate(CarouselValidate::class)->scene('position')->check($param);

        $data = CarouselService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("轮播是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\CarouselModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(CarouselValidate::class)->scene('disable')->check($param);

        $data = CarouselService::edit($param['ids'], $param);

        return success($data);
    }
}
