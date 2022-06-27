<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口管理控制器
namespace app\admin\controller\setting;

use think\facade\Request;
use app\common\validate\setting\ApiValidate;
use app\common\service\setting\ApiService;
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
     * @Apidoc\Returned("list", ref="app\common\model\setting\ApiModel\listReturn", type="array", desc="列表")
     * @Apidoc\Returned("tree", ref="app\common\model\setting\ApiModel\listReturn", type="tree", childrenField="children", desc="树形")
     */
    public function list()
    {
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        $where = [];
        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['api_id', 'api_pid', 'is_unauth', 'is_disable'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        if ($where) {
            $data['list'] = ApiService::list('list', $where);
        } else {
            $data['list'] = ApiService::list('tree', $where);
        }
        $data['tree'] = ApiService::list('tree', [], [], 'api_id,api_pid,api_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("接口信息")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\id")
     * @Apidoc\Returned(ref="app\common\model\setting\ApiModel\infoReturn")
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
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\addParam")
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
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\editParam")
     */
    public function edit()
    {
        $param['api_id']   = Request::param('api_id/d', 0);
        $param['api_pid']  = Request::param('api_pid/d', 0);
        $param['api_name'] = Request::param('api_name/s', '');
        $param['api_url']  = Request::param('api_url/s', '');
        $param['api_sort'] = Request::param('api_sort/d', 250);

        validate(ApiValidate::class)->scene('edit')->check($param);

        $data = ApiService::edit($param['api_id'], $param);

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
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\api_pid")
     */
    public function pid()
    {
        $param['ids']     = Request::param('ids/a', '');
        $param['api_pid'] = Request::param('api_pid/d', 0);

        validate(ApiValidate::class)->scene('pid')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口是否免登")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\is_unlogin")
     */
    public function unlogin()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_unlogin'] = Request::param('is_unlogin/d', 0);

        validate(ApiValidate::class)->scene('unlogin')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(ApiValidate::class)->scene('disable')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }
}
