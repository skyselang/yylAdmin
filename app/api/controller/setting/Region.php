<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\setting\RegionValidate;
use app\common\service\setting\RegionService;

/**
 * @Apidoc\Title("lang(地区)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("700")
 */
class Region extends BaseController
{
    /**
     * @Apidoc\Title("lang(地区列表)")
     * @Apidoc\Query("region_id", type="int", require=false, default="0", desc="地区id")
     * @Apidoc\Returned("list", ref={RegionService::class,"info"}, type="array", desc="地区列表", field="region_id,region_pid,region_name,pinyin,citycode,zipcode,longitude,latitude,sort")
     */
    public function list()
    {
        $region_pid = $this->param('region_id/d', 0);

        $where = where_disdel(['region_pid', '=', $region_pid]);

        $data['list'] = RegionService::list('list', $where);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(地区树形)")
     * @Apidoc\Returned("list", ref={RegionService::class,"info"}, type="tree", desc="地区树形", field="region_id,region_pid,region_name")
     */
    public function tree()
    {
        $where = where_disdel();

        $data['list'] = RegionService::list('tree', $where, [], 'region_pid,region_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(地区信息)")
     * @Apidoc\Query(ref={RegionService::class,"info"})
     * @Apidoc\Returned(ref={RegionService::class,"info"})
     */
    public function info()
    {
        $param = $this->params(['region_id/d' => '']);

        validate(RegionValidate::class)->scene('info')->check($param);

        $data = RegionService::info($param['region_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error(lang('地区不存在'));
        }

        return success($data);
    }
}
