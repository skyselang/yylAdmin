<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件分组控制器
namespace app\admin\controller\file;

use think\facade\Request;
use app\common\validate\file\GroupValidate;
use app\common\service\file\GroupService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件分组")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("91")
 */
class Group
{
    /**
     * @Apidoc\Title("文件分组列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="paramSort")
     * @Apidoc\Param("group_name", type="string", default="", desc="文件分组名称")
     * @Apidoc\Param("group_desc", type="string", default="", desc="文件分组描述")
     * @Apidoc\Returned(ref="returnPaging")
     * @Apidoc\Returned("list", type="array", desc="数据列表", 
     *     @Apidoc\Returned(ref="app\common\model\file\GroupModel\list")
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 9999);
        $sort_field = Request::param('sort_field/s', '');
        $sort_value = Request::param('sort_value/s', '');
        $group_name = Request::param('group_name/s', '');
        $group_desc = Request::param('group_desc/s', '');

        $where[] = ['is_delete', '=', 0];
        if ($group_name) {
            $where[] = ['group_name', 'like', '%' . $group_name . '%'];
        }
        if ($group_desc) {
            $where[] = ['group_desc', 'like', '%' . $group_desc . '%'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $field = 'group_id,group_name,group_desc,group_sort,is_disable,create_time,update_time';

        $data = GroupService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组信息")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\id")
     * @Apidoc\Returned(ref="app\common\model\file\GroupModel\info")
     */
    public function info()
    {
        $param['group_id'] = Request::param('group_id/d', '');

        validate(GroupValidate::class)->scene('info')->check($param);

        $data = GroupService::info($param['group_id']);
        if ($data['is_delete'] == 1) {
            exception('文件分组已被删除：' . $param['group_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\add")
     */
    public function add()
    {
        $param['group_name'] = Request::param('group_name/s', '');
        $param['group_desc'] = Request::param('group_desc/s', '');
        $param['group_sort'] = Request::param('group_sort/d', 50);
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(GroupValidate::class)->scene('add')->check($param);

        $data = GroupService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\edit")
     */
    public function edit()
    {
        $param['group_id']   = Request::param('group_id/d', '');
        $param['group_name'] = Request::param('group_name/s', '');
        $param['group_desc'] = Request::param('group_desc/s', '');
        $param['group_sort'] = Request::param('group_sort/d', 50);
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(GroupValidate::class)->scene('edit')->check($param);

        $data = GroupService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\group")
     */
    public function dele()
    {
        $param['group'] = Request::param('group/a', '');

        validate(GroupValidate::class)->scene('dele')->check($param);

        $data = GroupService::dele($param['group']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\group")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\disable")
     */
    public function disable()
    {
        $param['group']      = Request::param('group/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(GroupValidate::class)->scene('disable')->check($param);

        $data = GroupService::disable($param['group'], $param['is_disable']);

        return success($data);
    }
}
