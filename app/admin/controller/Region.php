<?php
/*
 * @Description  : 地区管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-08
 * @LastEditTime : 2020-12-17
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
        $region_pid      = Request::param('region_pid/d', 0) ?: 0;
        $region_name     = Request::param('region_name/s', '');
        $region_pinyin   = Request::param('region_pinyin/s', '');
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');

        if ($region_name || $region_pinyin) {
            if ($region_name) {
                $where[] = ['region_name', '=', $region_name];
            }
            if ($region_pinyin) {
                $where[] = ['region_pinyin', '=', $region_pinyin];
            }
        } else {
            $where[] = ['region_pid', '=', $region_pid];
        }

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
        $param['region_id'] = Request::param('region_id/d', '');

        validate(RegionValidate::class)->scene('region_id')->check($param);

        $data = RegionService::info($param['region_id']);

        if ($data['is_delete'] == 1) {
            exception('地区已被删除：' . $param['region_id']);
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
            $data = RegionService::add();
        } else {
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
            $param['region_sort']      = Request::param('region_sort/d', 1000);

            if (empty($param['region_pid'])) {
                $param['region_pid'] = 0;
            }

            if (empty($param['region_level'])) {
                $param['region_level'] = 1;
            }

            validate(RegionValidate::class)->scene('region_add')->check($param);

            $data = RegionService::add($param, 'post');
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
        $param['region_id'] = Request::param('region_id/d', '');

        if (Request::isGet()) {
            validate(RegionValidate::class)->scene('region_id')->check($param);

            $data = RegionService::edit($param);

            if ($data['is_delete'] == 1) {
                exception('地区已被删除');
            }
        } else {
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
            $param['region_sort']      = Request::param('region_sort/d', 1000);

            if (empty($param['region_pid'])) {
                $param['region_pid'] = 0;
            }

            if (empty($param['region_level'])) {
                $param['region_level'] = 1;
            }

            validate(RegionValidate::class)->scene('region_edit')->check($param);

            $data = RegionService::edit($param, 'post');
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
        $param['region_id'] = Request::param('region_id/d', '');

        validate(RegionValidate::class)->scene('region_dele')->check($param);

        $data = RegionService::dele($param['region_id']);

        return success($data);
    }
}
