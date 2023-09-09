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
use app\common\validate\file\GroupValidate;
use app\common\service\file\GroupService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件分组")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("200")
 */
class Group extends BaseController
{
    /**
     * @Apidoc\Title("文件分组列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\GroupModel", type="array", desc="分组列表", field="group_id,group_name,group_desc,remark,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = GroupService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组信息")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel", field="group_id")
     * @Apidoc\Returned(ref="app\common\model\file\GroupModel")
     */
    public function info()
    {
        $param = $this->params(['group_id/d' => '']);

        validate(GroupValidate::class)->scene('info')->check($param);

        $data = GroupService::info($param['group_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel", field="group_name,group_desc,remark,sort")
     */
    public function add()
    {
        $param = $this->params(GroupService::$edit_field);

        validate(GroupValidate::class)->scene('add')->check($param);

        $data = GroupService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel", field="group_id,group_name,group_desc,remark,sort")
     */
    public function edit()
    {
        $param = $this->params(GroupService::$edit_field);

        validate(GroupValidate::class)->scene('edit')->check($param);

        $data = GroupService::edit($param['group_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(GroupValidate::class)->scene('dele')->check($param);

        $data = GroupService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(GroupValidate::class)->scene('disable')->check($param);

        $data = GroupService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组文件")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\file\GroupModel", field="group_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="文件列表", children={
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel", field="file_id,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_size,file_ext,sort,is_disable,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getGroupNameAttr", field="group_name"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getTagNamesAttr", field="tag_names"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getFileTypeNameAttr", field="file_type_name"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getFileUrlAttr", field="file_url"),
     * })
     */
    public function file()
    {
        $param = $this->params(['group_id/d' => '']);

        validate(GroupValidate::class)->scene('file')->check($param);

        $where = $this->where(where_delete(['group_id', '=', $param['group_id']]));

        $data = GroupService::file($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组文件解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("group_id", type="array", require=true, desc="分组id")
     * @Apidoc\Param("file_ids", type="array", require=false, desc="文件id，为空则解除所有文件")
     */
    public function fileRemove()
    {
        $param = $this->params(['group_id/a' => [], 'file_ids/a' => []]);

        validate(GroupValidate::class)->scene('fileRemove')->check($param);

        $data = GroupService::fileRemove($param['group_id'], $param['file_ids']);

        return success($data);
    }
}
