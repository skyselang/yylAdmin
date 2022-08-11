<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\BaseController;
use app\common\validate\setting\RegionValidate;
use app\common\service\setting\RegionService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("地区管理")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("530")
 */
class Region extends BaseController
{
    /**
     * @Apidoc\Title("地区列表")
     * @Apidoc\Param("type", type="string", default="list", desc="list列表，tree树形")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\id")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned("list", ref="app\common\model\setting\RegionModel\listReturn", type="array", desc="地区列表")
     * @Apidoc\Returned("tree", ref="app\common\model\setting\RegionModel\treeReturn", type="tree", childrenField="children", desc="地区树形")
     */
    public function list()
    {
        $region_pid = $this->param('region_pid/d', 0);
        
        $where = ['region_pid', '=', $region_pid];
        $where = $this->where($where, 'region_id,region_pid,region_jianpin,region_initials,region_citycode,region_zipcode');
        if ($where) {
            $data['list'] = RegionService::list('list', $where, $this->order());
        } else {
            $data['list'] = RegionService::list('tree', $where, $this->order());
        }
        $data['tree'] = RegionService::list('tree', [], $this->order(), 'region_id,region_pid,region_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("地区信息")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\id")
     * @Apidoc\Returned(ref="app\common\model\setting\RegionModel\infoReturn")
     */
    public function info()
    {
        $param['region_id'] = $this->param('region_id/d', '');

        validate(RegionValidate::class)->scene('info')->check($param);

        $data = RegionService::info($param['region_id']);
        if ($data['is_delete'] == 1) {
            exception('地区已被删除：' . $param['region_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("地区添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\addParam")
     */
    public function add()
    {
        $param['region_pid']       = $this->param('region_pid/d', 0);
        $param['region_level']     = $this->param('region_level/d', 1);
        $param['region_name']      = $this->param('region_name/s', '');
        $param['region_pinyin']    = $this->param('region_pinyin/s', '');
        $param['region_jianpin']   = $this->param('region_jianpin/s', '');
        $param['region_initials']  = $this->param('region_initials/s', '');
        $param['region_citycode']  = $this->param('region_citycode/s', '');
        $param['region_zipcode']   = $this->param('region_zipcode/s', '');
        $param['region_longitude'] = $this->param('region_longitude/s', '');
        $param['region_latitude']  = $this->param('region_latitude/s', '');
        $param['region_sort']      = $this->param('region_sort/d', 2250);

        if (empty($param['region_pid'])) {
            $param['region_pid'] = 0;
        }
        if (empty($param['region_level'])) {
            $param['region_level'] = 1;
        }

        validate(RegionValidate::class)->scene('add')->check($param);

        $data = RegionService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\editParam")
     */
    public function edit()
    {
        $param['region_id']        = $this->param('region_id/d', '');
        $param['region_pid']       = $this->param('region_pid/d', 0);
        $param['region_level']     = $this->param('region_level/d', 1);
        $param['region_name']      = $this->param('region_name/s', '');
        $param['region_pinyin']    = $this->param('region_pinyin/s', '');
        $param['region_jianpin']   = $this->param('region_jianpin/s', '');
        $param['region_initials']  = $this->param('region_initials/s', '');
        $param['region_citycode']  = $this->param('region_citycode/s', '');
        $param['region_zipcode']   = $this->param('region_zipcode/s', '');
        $param['region_longitude'] = $this->param('region_longitude/s', '');
        $param['region_latitude']  = $this->param('region_latitude/s', '');
        $param['region_sort']      = $this->param('region_sort/d', 2250);

        if (empty($param['region_pid'])) {
            $param['region_pid'] = 0;
        }
        if (empty($param['region_level'])) {
            $param['region_level'] = 1;
        }

        validate(RegionValidate::class)->scene('edit')->check($param);

        $data = RegionService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(RegionValidate::class)->scene('dele')->check($param);

        $data = RegionService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\region_pid")
     */
    public function pid()
    {
        $param['ids']        = $this->param('ids/a', '');
        $param['region_pid'] = $this->param('region_pid/d', 0);

        validate(RegionValidate::class)->scene('pid')->check($param);

        $data = RegionService::pid($param['ids'], $param['region_pid']);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改区号")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\region_citycode")
     */
    public function citycode()
    {
        $param['ids']             = $this->param('ids/a', '');
        $param['region_citycode'] = $this->param('region_citycode/d', 0);

        validate(RegionValidate::class)->scene('citycode')->check($param);

        $data = RegionService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改邮编")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\region_zipcode")
     */
    public function zipcode()
    {
        $param['ids']            = $this->param('ids/a', '');
        $param['region_zipcode'] = $this->param('region_zipcode/d', 0);

        validate(RegionValidate::class)->scene('zipcode')->check($param);

        $data = RegionService::update($param['ids'], $param);

        return success($data);
    }
}
