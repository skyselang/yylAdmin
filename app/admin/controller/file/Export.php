<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\file;

use app\common\controller\BaseController;
use app\common\validate\file\ExportValidate;
use app\common\service\file\ExportService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("导出文件")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("600")
 */
class Export extends BaseController
{
    /**
     * @Apidoc\Title("导出文件列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="导出文件列表", children={
     *   @Apidoc\Returned(ref="app\common\model\file\ExportModel", field="export_id,type,file_name,file_path,file_size,times,remark,create_uid,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref="app\common\model\file\ExportModel\createUser"),
     *   @Apidoc\Returned(ref="app\common\model\file\ExportModel\getFileUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\file\ExportModel\getTypeNameAttr"),
     * })
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = ExportService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        $data['types'] = ExportService::types();
        $data['statuss'] = ExportService::statuss();

        return success($data);
    }

    /**
     * @Apidoc\Title("导出文件信息")
     * @Apidoc\Query(ref="app\common\model\file\ExportModel", field="export_id")
     * @Apidoc\Query("is_down", type="int", desc="是否下载文件，1是，0否")
     * @Apidoc\Returned(ref="app\common\model\file\ExportModel")
     * @Apidoc\Returned(ref="app\common\model\file\ExportModel\createUser")
     * @Apidoc\Returned(ref="app\common\model\file\ExportModel\getFileUrlAttr")
     * @Apidoc\Returned(ref="app\common\model\file\ExportModel\getTypeNameAttr")
     */
    public function info()
    {
        $param = $this->params(['export_id/d' => '', 'is_down/d' => 0]);

        validate(ExportValidate::class)->scene('info')->check($param);

        $data = ExportService::info($param['export_id']);

        if ($param['is_down']) {
            return download($data['file_path'], $data['file_name']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("导出文件修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\ExportModel", field="export_id,remark")
     */
    public function edit()
    {
        $param = $this->params(ExportService::$edit_field);

        validate(ExportValidate::class)->scene('edit')->check($param);

        $data = ExportService::edit($param['export_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("导出文件删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ExportValidate::class)->scene('dele')->check($param);

        $data = ExportService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("导出文件回收站列表")
     * @Apidoc\Desc("请求和返回参数同导出文件列表")
     */
    public function recycleList()
    {
        $where = $this->where(where_delete([], 1));
        
        $order = $this->order(['delete_time' => 'desc', 'export_id' => 'desc']);

        $data = ExportService::list($where, $this->page(), $this->limit(), $order);
        $data['exps'] = where_exps();
        $data['types'] = ExportService::types();
        $data['statuss'] = ExportService::statuss();

        return success($data);
    }

    /**
     * @Apidoc\Title("导出文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleReco()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ExportValidate::class)->scene('recycleReco')->check($param);

        $data = ExportService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("导出文件回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleDele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ExportValidate::class)->scene('recycleDele')->check($param);

        $data = ExportService::dele($param['ids'], true);

        return success($data);
    }
}
