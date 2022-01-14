<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 地区管理控制器
namespace app\admin\controller;

use think\facade\Request;
use app\common\service\RegionService;
use app\common\validate\RegionValidate;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("地区管理")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("530")
 */
class Region
{
    /**
     * @Apidoc\Title("地区列表")
     * @Apidoc\Param("type", type="string", default="list", desc="数据类型：list列表，tree树形")
     * @Apidoc\Param(ref="app\common\model\RegionModel\id")
     * @Apidoc\Param(ref="app\common\model\RegionModel\region_pid")
     * @Apidoc\Param(ref="app\common\model\RegionModel\region_name")
     * @Apidoc\Param(ref="app\common\model\RegionModel\region_pinyin")
     * @Apidoc\Returned("list", type="array", desc="地区列表", 
     *     @Apidoc\Returned(ref="app\common\model\RegionModel\listReturn")
     * )
     */
    public function list()
    {
        $type         = Request::param('type/s', 'list');
        $region_pid   = Request::param('region_pid/d', 0);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        if ($type == 'tree') {
            $data = RegionService::info('tree');
        } else {
            if ($search_field && $search_value) {
                if (in_array($search_field, ['region_id', 'region_pid', 'region_jianpin', 'region_initials', 'region_citycode', 'region_zipcode'])) {
                    $exp = strpos($search_value, ',') ? 'in' : '=';
                    $where[] = [$search_field, $exp, $search_value];
                } else {
                    if (strpos($search_value, ',')) {
                        $exp = 'in';
                    } else {
                        $exp = 'like';
                        $search_value = '%' . $search_value . '%';
                    }
                    $where[] = [$search_field, $exp, $search_value];
                }
            } else {
                $where[] = ['region_pid', '=', $region_pid];
            }
            if ($date_field && $date_value) {
                $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
                $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
            }

            $order = [];
            if ($sort_field && $sort_value) {
                $order = [$sort_field => $sort_value];
            }

            $data = RegionService::list($where, $order);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("地区信息")
     * @Apidoc\Param(ref="app\common\model\RegionModel\id")
     * @Apidoc\Returned(ref="app\common\model\RegionModel\infoReturn")
     */
    public function info()
    {
        $param['region_id'] = Request::param('region_id/d', '');

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
     * @Apidoc\Param(ref="app\common\model\RegionModel\addParam")
     */
    public function add()
    {
        $param['region_pid']       = Request::param('region_pid/d', 0);
        $param['region_level']     = Request::param('region_level/d', 1);
        $param['region_name']      = Request::param('region_name/s', '');
        $param['region_pinyin']    = Request::param('region_pinyin/s', '');
        $param['region_jianpin']   = Request::param('region_jianpin/s', '');
        $param['region_initials']  = Request::param('region_initials/s', '');
        $param['region_citycode']  = Request::param('region_citycode/s', '');
        $param['region_zipcode']   = Request::param('region_zipcode/s', '');
        $param['region_longitude'] = Request::param('region_longitude/s', '');
        $param['region_latitude']  = Request::param('region_latitude/s', '');
        $param['region_sort']      = Request::param('region_sort/d', 2250);

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
     * @Apidoc\Param(ref="app\common\model\RegionModel\editParam")
     */
    public function edit()
    {
        $param['region_id']        = Request::param('region_id/d', '');
        $param['region_pid']       = Request::param('region_pid/d', 0);
        $param['region_level']     = Request::param('region_level/d', 1);
        $param['region_name']      = Request::param('region_name/s', '');
        $param['region_pinyin']    = Request::param('region_pinyin/s', '');
        $param['region_jianpin']   = Request::param('region_jianpin/s', '');
        $param['region_initials']  = Request::param('region_initials/s', '');
        $param['region_citycode']  = Request::param('region_citycode/s', '');
        $param['region_zipcode']   = Request::param('region_zipcode/s', '');
        $param['region_longitude'] = Request::param('region_longitude/s', '');
        $param['region_latitude']  = Request::param('region_latitude/s', '');
        $param['region_sort']      = Request::param('region_sort/d', 2250);

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
        $param['ids'] = Request::param('ids/a', '');

        validate(RegionValidate::class)->scene('dele')->check($param);

        $data = RegionService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("地区修改父级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\RegionModel\region_pid")
     */
    public function pid()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['region_pid'] = Request::param('region_pid/d', 0);

        validate(RegionValidate::class)->scene('pid')->check($param);

        $data = RegionService::pid($param['ids'], $param['region_pid']);

        return success($data);
    }
}
