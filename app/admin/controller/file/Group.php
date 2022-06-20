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
 * @Apidoc\Sort("420")
 */
class Group
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
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', []);

        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['group_id', 'is_disable'])) {
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

        $data = GroupService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组信息")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\id")
     * @Apidoc\Returned(ref="app\common\model\file\GroupModel\infoReturn")
     */
    public function info()
    {
        $param['group_id'] = Request::param('group_id/d', '');

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
        $param['group_name'] = Request::param('group_name/s', '');
        $param['group_desc'] = Request::param('group_desc/s', '');
        $param['group_sort'] = Request::param('group_sort/d', 250);

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
        $param['group_id']   = Request::param('group_id/d', '');
        $param['group_name'] = Request::param('group_name/s', '');
        $param['group_desc'] = Request::param('group_desc/s', '');
        $param['group_sort'] = Request::param('group_sort/d', 250);

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
        $param['ids'] = Request::param('ids/a', []);

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
        $param['ids']        = Request::param('ids/a', []);
        $param['is_disable'] = Request::param('is_disable/d', 0);

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
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', []);

        if ($search_field && $search_value) {
            if (in_array($search_field, ['group_id'])) {
                $exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $exp, $search_value];
            } elseif (in_array($search_field, ['is_disable'])) {
                if ($search_value == '是' || $search_value == '1') {
                    $search_value = 1;
                } else {
                    $search_value = 0;
                }
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 1];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        } else {
            $order = ['delete_time' => 'desc', 'group_sort' => 'desc'];
        }

        $data = GroupService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids']       = Request::param('ids/a', []);
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
        $param['ids'] = Request::param('ids/a', []);

        validate(GroupValidate::class)->scene('recoverDele')->check($param);

        $data = GroupService::dele($param['ids'], true);

        return success($data);
    }
}
