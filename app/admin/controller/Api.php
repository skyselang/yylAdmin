<?php
/*
 * @Description  : 接口管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\ApiValidate;
use app\common\service\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("接口管理")
 * @Apidoc\Group("index")
 */
class Api
{
    /**
     * @Apidoc\Title("接口列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Returned(ref="return"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ApiModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data = ApiService::list();

        return success($data);
    }

    /**
     * @Apidoc\Title("接口信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiModel\id")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\ApiModel\info")
     * )
     */
    public function info()
    {
        $param['api_id'] = Request::param('api_id/d', '');

        validate(ApiValidate::class)->scene('info')->check($param);

        $data = ApiService::info($param['api_id']);

        if ($data['is_delete'] == 1) {
            exception('接口已删除：' . $param['api_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("接口添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiModel\add")
     * @Apidoc\Returned(ref="return")
     */
    public function add()
    {
        $param['api_pid']  = Request::param('api_pid/d', 0);
        $param['api_name'] = Request::param('api_name/s', '');
        $param['api_url']  = Request::param('api_url/s', '');
        $param['api_sort'] = Request::param('api_sort/d', 200);

        validate(ApiValidate::class)->scene('add')->check($param);

        $data = ApiService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiModel\edit")
     * @Apidoc\Returned(ref="return")
     */
    public function edit()
    {
        $param['api_id']   = Request::param('api_id/d', '');
        $param['api_pid']  = Request::param('api_pid/d', 0);
        $param['api_name'] = Request::param('api_name/s', '');
        $param['api_url']  = Request::param('api_url/s', '');
        $param['api_sort'] = Request::param('api_sort/d', 200);

        validate(ApiValidate::class)->scene('edit')->check($param);

        $data = ApiService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiModel\dele")
     * @Apidoc\Returned(ref="return")
     */
    public function dele()
    {
        $param['api_id'] = Request::param('api_id/d', '');

        validate(ApiValidate::class)->scene('dele')->check($param);

        $data = ApiService::dele($param['api_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiModel\disable")
     * @Apidoc\Returned(ref="return")
     */
    public function disable()
    {
        $param['api_id']     = Request::param('api_id/d', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(ApiValidate::class)->scene('disable')->check($param);

        $data = ApiService::disable($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口是否无需权限")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiModel\unauth")
     * @Apidoc\Returned(ref="return")
     */
    public function unauth()
    {
        $param['api_id']    = Request::param('api_id/d', '');
        $param['is_unauth'] = Request::param('is_unauth/d', 0);

        validate(ApiValidate::class)->scene('unauth')->check($param);

        $data = ApiService::unauth($param);

        return success($data);
    }
}
