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
use app\common\validate\setting\RegionValidate;
use app\common\service\setting\RegionService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("地区管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("500")
 */
class Region extends BaseController
{
    /**
     * @Apidoc\Title("地区列表")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Query(ref="app\common\model\setting\RegionModel", field="region_pid")
     * @Apidoc\Returned("list", ref="app\common\model\setting\RegionModel", type="array", desc="地区列表", field="region_id,region_pid,region_name,region_pinyin,region_citycode,region_zipcode,region_longitude,region_latitude,sort")
     * @Apidoc\Returned("tree", ref="app\common\model\setting\RegionModel", type="tree", desc="地区树形", field="region_id,region_pid,region_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());
        if (count($where) == 1) {
            $where[] = ['region_pid', '=', $this->request->param('region_pid/d', 0)];
        }

        $data['list']  = RegionService::list('list', $where, $this->order());
        $data['tree']  = RegionService::list('tree', [where_delete()], $this->order(), 'region_id,region_pid,region_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("地区信息")
     * @Apidoc\Query(ref="app\common\model\setting\RegionModel", field="region_id")
     * @Apidoc\Returned(ref="app\common\model\setting\RegionModel")
     * @Apidoc\Returned(ref="app\common\service\setting\RegionService\info")
     */
    public function info()
    {
        $param['region_id'] = $this->request->param('region_id/d', 0);

        validate(RegionValidate::class)->scene('info')->check($param);

        $data = RegionService::info($param['region_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="region_pid,region_level,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_longitude,region_latitude,sort")
     */
    public function add()
    {
        $param = $this->params(RegionService::$edit_field);

        validate(RegionValidate::class)->scene('add')->check($param);

        $data = RegionService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="region_id,region_pid,region_level,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_longitude,region_latitude,sort")
     */
    public function edit()
    {
        $param = $this->params(RegionService::$edit_field);

        validate(RegionValidate::class)->scene('edit')->check($param);

        $data = RegionService::edit($param['region_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(RegionValidate::class)->scene('dele')->check($param);

        $data = RegionService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="region_pid")
     */
    public function editpid()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['region_pid'] = $this->request->param('region_pid/d', 0);

        validate(RegionValidate::class)->scene('editpid')->check($param);

        $data = RegionService::editpid($param['ids'], $param['region_pid']);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改区号")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="region_citycode")
     */
    public function citycode()
    {
        $param['ids']             = $this->request->param('ids/a', []);
        $param['region_citycode'] = $this->request->param('region_citycode/s', '');

        validate(RegionValidate::class)->scene('citycode')->check($param);

        $data = RegionService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改邮编")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="region_zipcode")
     */
    public function zipcode()
    {
        $param['ids']            = $this->request->param('ids/a', []);
        $param['region_zipcode'] = $this->request->param('region_zipcode/s', '');

        validate(RegionValidate::class)->scene('zipcode')->check($param);

        $data = RegionService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(RegionValidate::class)->scene('disable')->check($param);

        $data = RegionService::update($param['ids'], $param);

        return success($data);
    }
}
