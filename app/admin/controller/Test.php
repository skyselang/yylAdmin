<?php
/*
 * @Description  : 测试控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 
 * @LastEditTime : 2021-06-09
 */
 
namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\TestValidate;
use app\common\service\TestService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("测试")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("999")
 */
class Test
{
    /**
     * @Apidoc\Title("测试列表")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("id", type="int", default="", desc="测试ID")
     * @Apidoc\Param("date_type", type="string", default="", desc="时间类型")
     * @Apidoc\Param("date_range", type="array", default="", desc="日期范围")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\TestModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page           = Request::param('page/d', 1);
        $limit          = Request::param('limit/d', 10);
        $sort_field     = Request::param('sort_field/s ', '');
        $sort_type      = Request::param('sort_type/s', '');
        $id = Request::param('id/d', '');
        $date_type      = Request::param('date_type/s', '');
        $date_range     = Request::param('date_range/a', []);

        $where = [];
        if ($id) {
            $where[] = ['id', '=', $id];
        }
        if ($date_type && $date_range) {
            $where[] = [$date_type, '>=', $date_range[0] . ' 00:00:00'];
            $where[] = [$date_type, '<=', $date_range[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = TestService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("测试信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\TestModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\TestModel\info")
     * )
     */
    public function info()
    {
        $param['id'] = Request::param('id/d', '');

        validate(TestValidate::class)->scene('info')->check($param);

        $data = TestService::info($param['id']);

        if ($data['is_delete'] == 1) {
            exception('测试已被删除：' . $param['id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("测试添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\TestModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function add()
    {
        $param = Request::param();

        validate(TestValidate::class)->scene('add')->check($param);

        $data = TestService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("测试修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\TestModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
    {
        $param['id'] = Request::param('id/d', '');

        validate(TestValidate::class)->scene('edit')->check($param);

        $data = TestService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("测试删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\TestModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['id'] = Request::param('id/d', '');

        validate(TestValidate::class)->scene('dele')->check($param);

        $data = TestService::dele($param['id']);

        return success($data);
    }
}