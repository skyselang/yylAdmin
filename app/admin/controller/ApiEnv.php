<?php
/*
 * @Description  : 接口环境
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-01-14
 * @LastEditTime : 2021-01-15
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\ApiEnvValidate;
use app\admin\service\ApiEnvService;

class ApiEnv
{
    /**
     * 接口环境列表
     *
     * @method GET
     * 
     * @return json
     */
    public function apiEnvList()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s', '');
        $sort_type  = Request::param('sort_type/s', '');
        $name       = Request::param('name/s', '');
        $host       = Request::param('host/s', '');

        $where = [];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($host) {
            $where[] = ['host', 'like', '%' . $host . '%'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = ApiEnvService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * 接口环境信息
     *
     * @method GET
     * 
     * @return json
     */
    public function apiEnvInfo()
    {
        $param['api_env_id'] = Request::param('api_env_id/d', '');

        validate(ApiEnvValidate::class)->scene('id')->check($param);

        $data = ApiEnvService::info($param['api_env_id']);

        if ($data['is_delete'] == 1) {
            exception('接口环境已删除：' . $param['api_env_id']);
        }

        return success($data);
    }

    /**
     * 接口环境添加
     *
     * @method POST
     * 
     * @return json
     */
    public function apiEnvAdd()
    {
        $param['name']   = Request::param('name/s', '');
        $param['host']   = Request::param('host/s', '');
        $param['header'] = Request::param('header/s', '');
        $param['remark'] = Request::param('remark/s', '');
        $param['sort']   = Request::param('sort/d', 200);

        validate(ApiEnvValidate::class)->scene('add')->check($param);

        $data = ApiEnvService::add($param);

        return success($data);
    }

    /**
     * 接口环境修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function apiEnvEdit()
    {
        $param['api_env_id'] = Request::param('api_env_id/d', '');

        if (Request::isGet()) {
            validate(ApiEnvValidate::class)->scene('id')->check($param);

            $data = ApiEnvService::edit($param);
        } else {
            $param['name']   = Request::param('name/s', '');
            $param['host']   = Request::param('host/s', '');
            $param['header'] = Request::param('header/s', '');
            $param['remark'] = Request::param('remark/s', '');
            $param['sort']   = Request::param('sort/d', 200);

            validate(ApiEnvValidate::class)->scene('edit')->check($param);

            $data = ApiEnvService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 接口环境删除
     *
     * @method POST
     * 
     * @return json
     */
    public function apiEnvDele()
    {
        $param['api_env_id'] = Request::param('api_env_id/d', '');

        validate(ApiEnvValidate::class)->scene('dele')->check($param);

        $data = ApiEnvService::dele($param['api_env_id']);

        return success($data);
    }
}
