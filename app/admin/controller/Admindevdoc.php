<?php
/*
 * @Description  : 开发文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-18
 * @LastEditTime : 2020-09-18
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminDevdocValidate;
use app\admin\service\AdminDevdocService;

class AdminDevdoc
{
    /**
     * 文档列表
     *
     * @method GET
     * 
     * @return json
     */
    public function devdocList()
    {
        $data = AdminDevdocService::list();

        return success($data);
    }

    /**
     * 文档信息
     *
     * @method GET
     * 
     * @return json
     */
    public function devdocInfo()
    {
        $admin_devdoc_id = Request::param('admin_devdoc_id/d', '');

        validate(AdminDevdocValidate::class)->scene('admin_devdoc_id')->check(['admin_devdoc_id' => $admin_devdoc_id]);

        $data = AdminDevdocService::info($admin_devdoc_id);

        return success($data);
    }

    /**
     * 文档添加
     *
     * @method POST
     * 
     * @return json
     */
    public function devdocAdd()
    {
        $param = Request::only(
            [
                'devdoc_pid'      => 0,
                'devdoc_name'     => '',
                'devdoc_sort'     => 200,
                'devdoc_content'  => '',
            ]
        );

        validate(AdminDevdocValidate::class)->scene('devdoc_add')->check($param);

        $data = AdminDevdocService::add($param);

        return success($data);
    }

    /**
     * 文档修改
     *
     * @method POST
     * 
     * @return json
     */
    public function devdocEdit()
    {
        $param = Request::only(
            [
                'admin_devdoc_id' => '',
                'devdoc_pid'      => 0,
                'devdoc_name'     => '',
                'devdoc_sort'     => 200,
                'devdoc_content'  => '',
            ]
        );

        validate(AdminDevdocValidate::class)->scene('devdoc_edit')->check($param);

        $data = AdminDevdocService::edit($param);

        return success($data);
    }

    /**
     * 文档删除
     *
     * @method POST
     * 
     * @return json
     */
    public function devdocDele()
    {
        $admin_devdoc_id = Request::param('admin_devdoc_id/d', '');

        validate(AdminDevdocValidate::class)->scene('admin_devdoc_id')->check(['admin_devdoc_id' => $admin_devdoc_id]);

        $data = AdminDevdocService::dele($admin_devdoc_id);

        return success($data);
    }
}
