<?php
/*
 * @Description  : 接口环境
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-01-14
 * @LastEditTime : 2021-05-25
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\ApiEnvValidate;
use app\common\service\ApiEnvService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("接口环境")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("60")
 */
class ApiEnv
{
    /**
     * @Apidoc\Title("接口环境列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("env_name", type="string", default="", desc="名称")
     * @Apidoc\Param("env_host", type="string", default="", desc="host")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\ApiEnvModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s', '');
        $sort_type  = Request::param('sort_type/s', '');
        $env_name   = Request::param('env_name/s', '');
        $env_host   = Request::param('env_host/s', '');

        $where = [];
        if ($env_name) {
            $where[] = ['env_name', 'like', '%' . $env_name . '%'];
        }
        if ($env_host) {
            $where[] = ['env_host', 'like', '%' . $env_host . '%'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = ApiEnvService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口环境信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiEnvModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\ApiEnvModel\info")
     * )
     */
    public function info()
    {
        $param['api_env_id'] = Request::param('api_env_id/d', '');

        validate(ApiEnvValidate::class)->scene('info')->check($param);

        $data = ApiEnvService::info($param['api_env_id']);

        if ($data['is_delete'] == 1) {
            exception('接口环境已被删除：' . $param['api_env_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("接口环境添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiEnvModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param['env_name']   = Request::param('env_name/s', '');
        $param['env_host']   = Request::param('env_host/s', '');
        $param['env_header'] = Request::param('env_header/s', '');
        $param['env_remark'] = Request::param('env_remark/s', '');
        $param['env_sort']   = Request::param('env_sort/d', 200);

        validate(ApiEnvValidate::class)->scene('add')->check($param);

        $data = ApiEnvService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口环境修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiEnvModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['api_env_id'] = Request::param('api_env_id/d', '');
        $param['env_name']   = Request::param('env_name/s', '');
        $param['env_host']   = Request::param('env_host/s', '');
        $param['env_header'] = Request::param('env_header/s', '');
        $param['env_remark'] = Request::param('env_remark/s', '');
        $param['env_sort']   = Request::param('env_sort/d', 200);

        validate(ApiEnvValidate::class)->scene('edit')->check($param);

        $data = ApiEnvService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口环境删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\ApiEnvModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['api_env_id'] = Request::param('api_env_id/d', '');

        validate(ApiEnvValidate::class)->scene('dele')->check($param);

        $data = ApiEnvService::dele($param['api_env_id']);

        return success($data);
    }
}
