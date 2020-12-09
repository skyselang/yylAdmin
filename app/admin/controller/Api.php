<?php
/*
 * @Description  : 接口管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-11-24
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\ApiValidate;
use app\admin\service\ApiService;

class Api
{
    /**
     * 接口列表
     *
     * @method GET
     * 
     * @return json
     */
    public function apiList()
    {
        $data = ApiService::list();

        return success($data);
    }

    /**
     * 接口信息
     *
     * @method GET
     * 
     * @return json
     */
    public function apiInfo()
    {
        $api_id = Request::param('api_id/d', '');

        $param['api_id'] = $api_id;

        validate(ApiValidate::class)->scene('api_id')->check($param);

        $data = ApiService::info($api_id);

        if ($data['is_delete'] == 1) {
            exception('接口已被删除');
        }

        return success($data);
    }

    /**
     * 接口添加
     *
     * @method POST
     * 
     * @return json
     */
    public function apiAdd()
    {
        $param = Request::only(
            [
                'api_pid'  => 0,
                'api_name' => '',
                'api_url'  => '',
                'api_sort' => 200,
            ]
        );

        validate(ApiValidate::class)->scene('api_add')->check($param);

        $data = ApiService::add($param);

        return success($data);
    }

    /**
     * 接口修改
     *
     * @method POST
     * 
     * @return json
     */
    public function apiEdit()
    {
        if (Request::isGet()) {
            $param['api_id'] = Request::param('api_id/d', '');

            validate(ApiValidate::class)->scene('api_id')->check($param);

            $data = ApiService::edit($param);
        } else {
            $param = Request::only(
                [
                    'api_id'   => '',
                    'api_pid'  => 0,
                    'api_name' => '',
                    'api_url'  => '',
                    'api_sort' => 200,
                ]
            );

            validate(ApiValidate::class)->scene('api_edit')->check($param);

            $data = ApiService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 接口删除
     *
     * @method POST
     * 
     * @return json
     */
    public function apiDele()
    {
        $api_id = Request::param('api_id/d', '');

        $param['api_id'] = $api_id;

        validate(ApiValidate::class)->scene('api_dele')->check($param);

        $data = ApiService::dele($api_id);

        return success($data);
    }

    /**
     * 接口是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function apiDisable()
    {
        $param = Request::only(
            [
                'api_id'     => '',
                'is_disable' => '0',
            ]
        );

        validate(ApiValidate::class)->scene('api_id')->check($param);

        $data = ApiService::disable($param);

        return success($data);
    }

    /**
     * 接口是否无需权限
     *
     * @method POST
     * 
     * @return json
     */
    public function apiUnauth()
    {
        $param = Request::only(
            [
                'api_id'    => '',
                'is_unauth' => '0',
            ]
        );

        validate(ApiValidate::class)->scene('api_id')->check($param);

        $data = ApiService::unauth($param);

        return success($data);
    }
}
