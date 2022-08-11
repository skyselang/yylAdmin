<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\file;

use app\common\BaseController;
use app\common\validate\file\GroupValidate;
use app\common\service\file\GroupService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件分组")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("420")
 */
class Group extends BaseController
{
    /**
     * @Apidoc\Title("文件分组列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\GroupModel\listReturn", type="array", desc="文件分组列表")
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'group_id,is_disable');

        $data = GroupService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组信息")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\id")
     * @Apidoc\Returned(ref="app\common\model\file\GroupModel\infoReturn")
     */
    public function info()
    {
        $param['group_id'] = $this->param('group_id/d', '');

        validate(GroupValidate::class)->scene('info')->check($param);

        $data = GroupService::info($param['group_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("group_name", mock="@ctitle(2, 5)")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\addParam")
     */
    public function add()
    {
        $param['group_name'] = $this->param('group_name/s', '');
        $param['group_desc'] = $this->param('group_desc/s', '');
        $param['group_sort'] = $this->param('group_sort/d', 250);

        validate(GroupValidate::class)->scene('add')->check($param);

        $data = GroupService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\editParam")
     */
    public function edit()
    {
        $param['group_id']   = $this->param('group_id/d', '');
        $param['group_name'] = $this->param('group_name/s', '');
        $param['group_desc'] = $this->param('group_desc/s', '');
        $param['group_sort'] = $this->param('group_sort/d', 250);

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
        $param['ids'] = $this->param('ids/a', []);

        validate(GroupValidate::class)->scene('dele')->check($param);

        $data = GroupService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->param('ids/a', []);
        $param['is_disable'] = $this->param('is_disable/d', 0);

        validate(GroupValidate::class)->scene('disable')->check($param);

        $data = GroupService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\GroupModel\listReturn", type="array", desc="文件分组列表")
     */
    public function recover()
    {
        $where = $this->where(['is_delete', '=', 1], 'group_id,is_disable');

        $order = ['delete_time' => 'desc', 'group_sort' => 'desc'];

        $data = GroupService::list($where, $this->page(), $this->limit(), $this->order($order));

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids']       = $this->param('ids/a', []);
        $param['is_delete'] = 0;

        validate(GroupValidate::class)->scene('recoverReco')->check($param);

        $data = GroupService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = $this->param('ids/a', []);

        validate(GroupValidate::class)->scene('recoverDele')->check($param);

        $data = GroupService::dele($param['ids'], true);

        return success($data);
    }
}
