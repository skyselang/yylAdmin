<?php
/*
 * @Description  : 地区管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-08
 * @LastEditTime : 2020-12-09
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\RegionService;
use app\admin\validate\RegionValidate;

class Region
{
    /**
     * 地区列表
     *
     * @method GET
     * 
     * @return json
     */
    public function regionList()
    {
        $region_pid = Request::param('region_pid/d', 0) ?: 0;
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');

        $where[] = ['is_delete', '=', 0];
        $where[] = ['region_pid', '=', $region_pid];

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = RegionService::list($where, $order);

        return success($data);
    }

    /**
     * 地区信息
     *
     * @method GET
     * 
     * @return json
     */
    public function regionInfo()
    {
        $region_id = Request::param('region_id/d', '');

        validate(RegionValidate::class)->scene('region_id')->check(['region_id' => $region_id]);

        $data = RegionService::info($region_id);

        if ($data['is_delete'] == -1) {
            exception('地区已被删除');
        }

        return success($data);
    }

    /**
     * 地区添加
     *
     * @method POST
     * 
     * @return json
     */
    public function regionAdd()
    {
        if (Request::isGet()) {
            $data = RegionService::add([], 'get');
        } else {
            $param = Request::only(
                [
                    'region_pid'       => 0,
                    'region_level'     => 1,
                    'region_name'      => '',
                    'region_pinyin'    => '',
                    'region_jianpin'   => '',
                    'region_initials'  => '',
                    'region_citycode'  => '',
                    'region_zipcode'   => '',
                    'region_longitude' => '',
                    'region_latitude'  => '',
                    'region_sort'      => 1000,
                ]
            );

            if (empty($param['region_pid'])) {
                $param['region_pid'] = 0;
            }

            if (empty($param['region_level'])) {
                $param['region_level'] = 1;
            }

            validate(RegionValidate::class)->scene('region_add')->check($param);

            $data = RegionService::add($param);
        }

        return success($data);
    }

    /**
     * 地区修改
     *
     * @method POST
     * 
     * @return json
     */
    public function regionEdit()
    {
        if (Request::isGet()) {
            $param['region_id'] = Request::param('region_id/d', '');

            validate(RegionValidate::class)->scene('region_id')->check($param);

            $data = RegionService::edit($param, 'get');

            if ($data['is_delete'] == -1) {
                exception('地区已被删除');
            }
        } else {
            $param = Request::only(
                [
                    'region_id'        => '',
                    'region_pid'       => 0,
                    'region_level'     => 1,
                    'region_name'      => '',
                    'region_pinyin'    => '',
                    'region_jianpin'   => '',
                    'region_initials'  => '',
                    'region_citycode'  => '',
                    'region_zipcode'   => '',
                    'region_longitude' => '',
                    'region_latitude'  => '',
                    'region_sort'      => 1000,
                ]
            );

            if (empty($param['region_pid'])) {
                $param['region_pid'] = 0;
            }

            if (empty($param['region_level'])) {
                $param['region_level'] = 1;
            }

            validate(RegionValidate::class)->scene('region_edit')->check($param);

            $data = RegionService::edit($param);
        }

        return success($data);
    }

    /**
     * 地区删除
     *
     * @method POST
     * 
     * @return json
     */
    public function regionDele()
    {
        $region_id = Request::param('region_id/d', '');

        validate(RegionValidate::class)->scene('region_dele')->check(['region_id' => $region_id]);

        $data = RegionService::dele($region_id);

        return success($data);
    }
}
