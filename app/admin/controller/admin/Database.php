<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\DatabaseValidate;
use app\common\service\admin\DatabaseService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("数据库管理")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("780")
 */
class Database extends BaseController
{
    /**
     * @Apidoc\Title("备份列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="extraParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\admin\DatabaseModel\listReturn", type="array", desc="备份列表")
     * @Apidoc\Returned("table", type="array", desc="数据库表", 
     *     @Apidoc\Returned("Name", type="string", desc="表名"), 
     *     @Apidoc\Returned("Comment", type="string", desc="注释"), 
     *     @Apidoc\Returned("Engine", type="string", desc="引擎"), 
     *     @Apidoc\Returned("Rows", type="string", desc="行"), 
     *     @Apidoc\Returned("Auto_increment", type="string", desc="自动递增值"), 
     *     @Apidoc\Returned("Collation", type="string", desc="排序规则"), 
     *     @Apidoc\Returned("Avg_row_length", type="string", desc="平均每行长度"), 
     *     @Apidoc\Returned("Index_length", type="string", desc="索引长度"), 
     *     @Apidoc\Returned("Data_length", type="string", desc="数据长度"), 
     *     @Apidoc\Returned("Max_data_length", type="string", desc="最大数据长度"), 
     *     @Apidoc\Returned("Data_free", type="string", desc="数据可用空间"), 
     *     @Apidoc\Returned("Row_format", type="string", desc="行格式"), 
     *     @Apidoc\Returned("Create_options", type="string", desc="创建选项"), 
     *     @Apidoc\Returned("Create_time", type="string", desc="创建日期"), 
     *     @Apidoc\Returned("is_ignore", type="int", desc="是否备份忽略的表"), 
     * )
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'admin_database_id,admin_user_id,username');

        $data = DatabaseService::list($where, $this->page(), $this->limit(), $this->order(), '', $this->isExtra());

        return success($data);
    }

    /**
     * @Apidoc\Title("备份信息")
     * @Apidoc\Param(ref="app\common\model\admin\DatabaseModel\id", desc="备份id，与表名二选一")
     * @Apidoc\Param("table_name", type="string", desc="表名，与备份id二选一")
     * @Apidoc\Returned(ref="app\common\model\admin\DatabaseModel\infoReturn", desc="备份信息")
     * @Apidoc\Returned("ddl", type="string", desc="表ddl信息")
     * @Apidoc\Returned("info", type="object", desc="表常规信息", 
     *     @Apidoc\Returned("Name", type="string", desc="表名"), 
     *     @Apidoc\Returned("Comment", type="string", desc="注释"), 
     *     @Apidoc\Returned("Engine", type="string", desc="引擎"), 
     *     @Apidoc\Returned("Rows", type="string", desc="行"), 
     *     @Apidoc\Returned("Auto_increment", type="string", desc="自动递增值"), 
     *     @Apidoc\Returned("Collation", type="string", desc="排序规则"), 
     *     @Apidoc\Returned("Avg_row_length", type="string", desc="平均每行长度"), 
     *     @Apidoc\Returned("Index_length", type="string", desc="索引长度"), 
     *     @Apidoc\Returned("Data_length", type="string", desc="数据长度"), 
     *     @Apidoc\Returned("Max_data_length", type="string", desc="最大数据长度"), 
     *     @Apidoc\Returned("Data_free", type="string", desc="数据可用空间"), 
     *     @Apidoc\Returned("Row_format", type="string", desc="行格式"), 
     *     @Apidoc\Returned("Create_options", type="string", desc="创建选项"), 
     *     @Apidoc\Returned("Create_time", type="string", desc="创建日期"), 
     *     @Apidoc\Returned("is_ignore", type="int", desc="是否备份忽略的表"), 
     * )
     */
    public function info()
    {
        $param['admin_database_id'] = $this->param('admin_database_id/d', '');
        $param['table_name']        = $this->param('table_name/s', '');

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
        $param['table']  = $this->param('table/a', []);
        $param['remark'] = $this->param('remark/s', '');

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
        $param['admin_database_id'] = $this->param('admin_database_id/d', '');
        $param['remark']            = $this->param('remark/s', '');

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
        $param['ids'] = $this->param('ids/a', '');

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
        $param['admin_database_id'] = $this->param('admin_database_id/d', '');

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
        $param['admin_database_id'] = $this->param('admin_database_id/d', '');

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
        $param['table'] = $this->param('table/a', []);

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
        $param['table'] = $this->param('table/a', []);

        validate(DatabaseValidate::class)->scene('repair')->check($param);

        $data = DatabaseService::repair($param['table']);

        return success($data);
    }
}
