<?php
/*
 * @Description  : 接口文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-17
 * @LastEditTime : 2020-09-18
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminApidocService;
use app\admin\validate\AdminApidocValidate;

class AdminApidoc
{
    /**
     * 文档列表
     *
     * @method GET
     * 
     * @return json
     */
    public function apidocList()
    {
        $data = AdminApidocService::list();

        return success($data);
    }

    /**
     * 文档信息
     *
     * @method GET
     * 
     * @return json
     */
    public function apidocInfo()
    {
        $admin_apidoc_id = Request::param('admin_apidoc_id/d', '');

        validate(AdminApidocValidate::class)->scene('admin_apidoc_id')->check(['admin_apidoc_id' => $admin_apidoc_id]);

        $data = AdminApidocService::info($admin_apidoc_id);

        return success($data);
    }

    /**
     * 文档添加
     *
     * @method POST
     * 
     * @return json
     */
    public function apidocAdd()
    {
        $param = Request::only(
            [
                'apidoc_pid'      => 0,
                'apidoc_name'     => '',
                'apidoc_method'   => '',
                'apidoc_sort'     => 200,
                'apidoc_path'     => '',
                'apidoc_request'  => '',
                'apidoc_response' => '',
                'apidoc_example'  => '',
                'apidoc_explain'  => '',
            ]
        );

        validate(AdminApidocValidate::class)->scene('apidoc_add')->check($param);

        $data = AdminApidocService::add($param);

        return success($data);
    }

    /**
     * 文档修改
     *
     * @method POST
     * 
     * @return json
     */
    public function apidocEdit()
    {
        $param = Request::only(
            [
                'admin_apidoc_id' => '',
                'apidoc_pid'      => 0,
                'apidoc_name'     => '',
                'apidoc_method'   => '',
                'apidoc_sort'     => 200,
                'apidoc_path'     => '',
                'apidoc_request'  => '',
                'apidoc_response' => '',
                'apidoc_example'  => '',
                'apidoc_explain'  => '',
            ]
        );

        validate(AdminApidocValidate::class)->scene('apidoc_edit')->check($param);

        $data = AdminApidocService::edit($param);

        return success($data);
    }

    /**
     * 文档删除
     *
     * @method POST
     * 
     * @return json
     */
    public function apidocDele()
    {
        $admin_apidoc_id = Request::param('admin_apidoc_id/d', '');

        validate(AdminApidocValidate::class)->scene('admin_apidoc_id')->check(['admin_apidoc_id' => $admin_apidoc_id]);

        $data = AdminApidocService::dele($admin_apidoc_id);

        return success($data);
    }
}
