<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\BaseController;
use app\common\validate\setting\ApiValidate;
use app\common\service\setting\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("接口管理")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("510")
 */
class Api extends BaseController
{
    /**
     * @Apidoc\Title("接口列表")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Returned("list", ref="app\common\model\setting\ApiModel\listReturn", type="array", desc="列表")
     * @Apidoc\Returned("tree", ref="app\common\model\setting\ApiModel\listReturn", type="tree", childrenField="children", desc="树形")
     */
    public function list()
    {
        $where = $this->where([], 'api_id,api_pid');

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
        $param['api_id'] = $this->param('api_id/d', '');

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
        $param['api_pid']  = $this->param('api_pid/d', 0);
        $param['api_name'] = $this->param('api_name/s', '');
        $param['api_url']  = $this->param('api_url/s', '');
        $param['api_sort'] = $this->param('api_sort/d', 250);

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
        $param['api_id']   = $this->param('api_id/d', 0);
        $param['api_pid']  = $this->param('api_pid/d', 0);
        $param['api_name'] = $this->param('api_name/s', '');
        $param['api_url']  = $this->param('api_url/s', '');
        $param['api_sort'] = $this->param('api_sort/d', 250);

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
        $param['ids'] = $this->param('ids/a', '');

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
        $param['ids']     = $this->param('ids/a', '');
        $param['api_pid'] = $this->param('api_pid/d', 0);

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
        $param['ids']        = $this->param('ids/a', '');
        $param['is_unlogin'] = $this->param('is_unlogin/d', 0);

        validate(ApiValidate::class)->scene('unlogin')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }

     /**
     * @Apidoc\Title("接口是否免限")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\ApiModel\is_unrate")
     */
    public function unrate()
    {
        $param['ids']       = $this->param('ids/a', '');
        $param['is_unrate'] = $this->param('is_unrate/d', 0);

        validate(ApiValidate::class)->scene('unrate')->check($param);

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
        $param['ids']        = $this->param('ids/a', '');
        $param['is_disable'] = $this->param('is_disable/d', 0);

        validate(ApiValidate::class)->scene('disable')->check($param);

        $data = ApiService::edit($param['ids'], $param);

        return success($data);
    }
}
