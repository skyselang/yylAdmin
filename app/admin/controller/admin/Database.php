<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 数据库管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\DatabaseValidate;
use app\common\service\admin\DatabaseService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("数据库管理")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("780")
 */
class Database
{
    /**
     * @Apidoc\Title("备份列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\DatabaseModel\listReturn")
     * )
     */
    public function list()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');
        $is_extra     = Request::param('is_extra/d', 0);

        if ($search_field && $search_value) {
            if (in_array($search_field, ['admin_database_id', 'admin_user_id', 'username'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 0];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = DatabaseService::list($where, $page, $limit, $order, '', $is_extra);

        return success($data);
    }

    /**
     * @Apidoc\Title("备份信息")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\DatabaseModel\infoReturn")
     */
    public function info()
    {
        $param['admin_database_id'] = Request::param('admin_database_id/d', '');
        $param['table_name']        = Request::param('table_name/s', '');

        if ($param['admin_database_id']) {
            validate(DatabaseValidate::class)->scene('info')->check($param);
            $data = DatabaseService::info($param['admin_database_id']);
            if ($data['is_delete'] == 1) {
                exception('备份已被删除：' . $param['admin_database_id']);
            }
        } elseif ($param['table_name']) {
            $data = DatabaseService::tableInfo($param['table_name']);
        } else {
            exception();
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("备份添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\addParam")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\table")
     */
    public function add()
    {
        $param['table']  = Request::param('table/a', []);
        $param['remark'] = Request::param('remark/s', '');

        validate(DatabaseValidate::class)->scene('add')->check($param);

        $data = DatabaseService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("备份修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\editParam")
     */
    public function edit()
    {
        $param['admin_database_id'] = Request::param('admin_database_id/d', '');
        $param['remark']            = Request::param('remark/s', '');

        validate(DatabaseValidate::class)->scene('edit')->check($param);

        $data = DatabaseService::edit($param['admin_database_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("备份删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(DatabaseValidate::class)->scene('dele')->check($param);

        $data = DatabaseService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("备份下载")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\id")
     */
    public function down()
    {
        $param['admin_database_id'] = Request::param('admin_database_id/d', '');

        validate(DatabaseValidate::class)->scene('down')->check($param);

        $data = DatabaseService::down($param['admin_database_id']);

        return download($data['path']);
    }

    /**
     * @Apidoc\Title("备份还原")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\id")
     */
    public function restore()
    {
        $param['admin_database_id'] = Request::param('admin_database_id/d', '');

        validate(DatabaseValidate::class)->scene('restore')->check($param);

        $data = DatabaseService::restore($param['admin_database_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("优化表")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\table")
     */
    public function optimize()
    {
        $param['table'] = Request::param('table/a', []);

        validate(DatabaseValidate::class)->scene('optimize')->check($param);

        $data = DatabaseService::optimize($param['table']);

        return success($data);
    }

    /**
     * @Apidoc\Title("修复表")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\table")
     */
    public function repair()
    {
        $param['table'] = Request::param('table/a', []);

        validate(DatabaseValidate::class)->scene('repair')->check($param);

        $data = DatabaseService::repair($param['table']);

        return success($data);
    }
}
