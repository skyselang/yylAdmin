<?php
/*
 * @Description  : 接口管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-10
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
        $param['api_id'] = Request::param('api_id/d', '');

        validate(ApiValidate::class)->scene('api_id')->check($param);

        $data = ApiService::info($param['api_id']);

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
        $param['api_pid']  = Request::param('api_pid/d', 0);
        $param['api_name'] = Request::param('api_name/s', '');
        $param['api_url']  = Request::param('api_url/s', '');
        $param['api_sort'] = Request::param('api_sort/d', 200);

        validate(ApiValidate::class)->scene('api_add')->check($param);

        $data = ApiService::add($param);

        return success($data);
    }

    /**
     * 接口修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function apiEdit()
    {
        $param['api_id'] = Request::param('api_id/d', '');

        if (Request::isGet()) {
            validate(ApiValidate::class)->scene('api_id')->check($param);

            $data = ApiService::edit($param);
        } else {
            $param['api_pid']  = Request::param('api_pid/d', 0);
            $param['api_name'] = Request::param('api_name/s', '');
            $param['api_url']  = Request::param('api_url/s', '');
            $param['api_sort'] = Request::param('api_sort/d', 200);

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
        $param['api_id'] = Request::param('api_id/d', '');

        validate(ApiValidate::class)->scene('api_dele')->check($param);

        $data = ApiService::dele($param['api_id']);

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
        $param['api_id']     = Request::param('api_id/d', '');
        $param['is_disable'] = Request::param('is_disable/s', '0');

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
        $param['api_id']    = Request::param('api_id/d', '');
        $param['is_unauth'] = Request::param('is_unauth/s', '0');

        validate(ApiValidate::class)->scene('api_id')->check($param);

        $data = ApiService::unauth($param);

        return success($data);
    }
}
