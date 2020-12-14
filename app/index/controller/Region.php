<?php
/*
 * @Description  : 地区
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-14
 * @LastEditTime : 2020-12-14
 */

namespace app\index\controller;

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

        $where[] = ['is_delete', '=', 0];
        $where[] = ['region_pid', '=', $region_pid];

        $order = [];

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
            exception('地区已被删除');
        }

        return success($data);
    }
}
