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
use app\common\validate\file\ImportValidate;
use app\common\service\file\ImportService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("导入文件")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("500")
 */
class Import extends BaseController
{
    /**
     * @Apidoc\Title("导入文件列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="导入文件列表", children={
     *   @Apidoc\Returned(ref="app\common\model\file\ImportModel", field="import_id,type,file_name,file_path,file_size,remark,create_uid,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref="app\common\model\file\ImportModel\createUser"),
     *   @Apidoc\Returned(ref="app\common\model\file\ImportModel\getFileUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\file\ImportModel\getTypeNameAttr"),
     * })
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = ImportService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        $data['types'] = ImportService::types();
        $data['statuss'] = ImportService::statuss();

        return success($data);
    }

    /**
     * @Apidoc\Title("导入文件信息")
     * @Apidoc\Query(ref="app\common\model\file\ImportModel", field="import_id")
     * @Apidoc\Query("is_down", type="int", desc="是否下载文件，1是，0否")
     * @Apidoc\Query("file_type", type="string", desc="下载文件类型，import导入文件，success成功文件，fail失败文件")
     * @Apidoc\Returned(ref="app\common\model\file\ImportModel")
     * @Apidoc\Returned(ref="app\common\model\file\ImportModel\createUser")
     * @Apidoc\Returned(ref="app\common\model\file\ImportModel\getFileUrlAttr")
     * @Apidoc\Returned(ref="app\common\model\file\ImportModel\getTypeNameAttr")
     */
    public function info()
    {
        $param = $this->params(['import_id/d' => '', 'is_down/d' => 0, 'file_type/s' => 'import']);

        validate(ImportValidate::class)->scene('info')->check($param);

        $data = ImportService::info($param['import_id']);

        if ($param['is_down']) {
            try {
                if ($param['file_type'] == 'success') {
                    return download($data['file_path_success']);
                } elseif ($param['file_type'] == 'fail') {
                    return download($data['file_path_fail']);
                } else {
                    return download($data['file_path']);
                }
            } catch (\Exception $e) {
                return error($e->getMessage());
            }
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("导入文件修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\ImportModel", field="import_id,remark")
     */
    public function edit()
    {
        $param = $this->params(ImportService::$edit_field);

        validate(ImportValidate::class)->scene('edit')->check($param);

        $data = ImportService::edit($param['import_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("导入文件删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ImportValidate::class)->scene('dele')->check($param);

        $data = ImportService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("导入文件回收站列表")
     * @Apidoc\Desc("请求和返回参数同导入文件列表")
     */
    public function recycleList()
    {
        $where = $this->where(where_delete([], 1));

        $order = $this->order(['delete_time' => 'desc', 'import_id' => 'desc']);

        $data = ImportService::list($where, $this->page(), $this->limit(), $order);
        $data['exps'] = where_exps();
        $data['types'] = ImportService::types();
        $data['statuss'] = ImportService::statuss();

        return success($data);
    }

    /**
     * @Apidoc\Title("导入文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleReco()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ImportValidate::class)->scene('recycleReco')->check($param);

        $data = ImportService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("导入文件回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleDele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ImportValidate::class)->scene('recycleDele')->check($param);

        $data = ImportService::dele($param['ids'], true);

        return success($data);
    }
}
