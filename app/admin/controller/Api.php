<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口管理控制器
namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\ApiValidate;
use app\common\service\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("接口管理")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("510")
 */
class Api
{
    /**
     * @Apidoc\Title("接口列表")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Returned("list", type="array", desc="树形列表", 
     *     @Apidoc\Returned(ref="app\common\model\ApiModel\listReturn")
     * )
     */
    public function list()
    {
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');

        $where = [];
        if ($search_field && $search_value) {
            if (in_array($search_field, ['api_id', 'api_pid'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['is_unauth', 'is_disable'])) {
                if ($search_value == '是' || $search_value == '1') {
                    $search_value = 1;
                } else {
                    $search_value = 0;
                }
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }

        $data['list'] = ApiService::list('tree', $where);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口信息")
     * @Apidoc\Param(ref="app\common\model\ApiModel\id")
     * @Apidoc\Returned(ref="app\common\model\ApiModel\infoReturn")
     */
    public function info()
    {
        $param['api_id'] = Request::param('api_id/d', '');

        validate(ApiValidate::class)->scene('info')->check($param);

        $data = ApiService::info($param['api_id']);
        if ($data['is_delete'] == 1) {
            exception('接口已被删除：' . $param['api_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("接口添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\ApiModel\addParam")
     */
    public function add()
    {
        $param['api_pid']  = Request::param('api_pid/d', 0);
        $param['api_name'] = Request::param('api_name/s', '');
        $param['api_url']  = Request::param('api_url/s', '');
        $param['api_sort'] = Request::param('api_sort/d', 250);

        validate(ApiValidate::class)->scene('add')->check($param);

        $data = ApiService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\ApiModel\editParam")
     */
    public function edit()
    {
        $param['api_id']   = Request::param('api_id/d', '');
        $param['api_pid']  = Request::param('api_pid/d', 0);
        $param['api_name'] = Request::param('api_name/s', '');
        $param['api_url']  = Request::param('api_url/s', '');
        $param['api_sort'] = Request::param('api_sort/d', 250);

        validate(ApiValidate::class)->scene('edit')->check($param);

        $data = ApiService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(ApiValidate::class)->scene('dele')->check($param);

        $data = ApiService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\ApiModel\api_pid")
     */
    public function pid()
    {
        $param['ids']     = Request::param('ids/a', '');
        $param['api_pid'] = Request::param('api_pid/d', 0);

        validate(ApiValidate::class)->scene('pid')->check($param);

        $data = ApiService::pid($param['ids'], $param['api_pid']);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口是否无需登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\ApiModel\is_unlogin")
     */
    public function unlogin()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_unlogin'] = Request::param('is_unlogin/d', 0);

        validate(ApiValidate::class)->scene('unlogin')->check($param);

        $data = ApiService::unlogin($param['ids'], $param['is_unlogin']);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\ApiModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(ApiValidate::class)->scene('disable')->check($param);

        $data = ApiService::disable($param['ids'], $param['is_disable']);

        return success($data);
    }
}
