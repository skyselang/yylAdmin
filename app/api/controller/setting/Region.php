<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use app\common\controller\BaseController;
use app\common\service\setting\RegionService;
use app\common\validate\setting\RegionValidate;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("地区")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("600")
 */
class Region extends BaseController
{
    /**
     * @Apidoc\Title("地区列表")
     * @Apidoc\Query("region_id", type="int", require=false, default="0", desc="地区id")
     * @Apidoc\Returned("list", ref="app\common\model\setting\RegionModel", type="array", desc="地区列表", field="region_id,region_pid,region_name,region_pinyin,region_citycode,region_zipcode,region_longitude,region_latitude,sort")
     */
    public function list()
    {
        $region_pid = $this->request->param('region_id/d', 0);

        $where = [['region_pid', '=', $region_pid], where_disable(), where_delete()];

        $data['list'] = RegionService::list('list', $where);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区树形")
     * @Apidoc\Returned("list", ref="app\common\model\setting\RegionModel", type="tree", desc="地区树形", field="region_id,region_pid,region_name")
     */
    public function tree()
    {
        $where = [where_disable(), where_delete()];

        $data['list'] = RegionService::list('tree', $where, [], 'region_id,region_pid,region_name');

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
        $param['region_id'] = $this->request->param('region_id/s', '');

        validate(RegionValidate::class)->scene('info')->check($param);

        $data = RegionService::info($param['region_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return success([], '地区不存在或已禁用或已删除');
        }

        return success($data);
    }
}
