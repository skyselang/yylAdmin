<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件管理控制器
namespace app\admin\controller\file;

use think\facade\Request;
use app\common\validate\file\FileValidate;
use app\common\service\file\GroupService;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件管理")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("410")
 */
class File
{
    /**
     * @Apidoc\Title("分组列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="分组列表", 
     *     @Apidoc\Returned(ref="app\common\model\file\GroupModel\listReturn")
     * )
     */
    public function group()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 9999);
        $sort_field = Request::param('sort_field/s', '');
        $sort_value = Request::param('sort_value/s', '');

        $where[] = ['is_delete', '=', 0];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $field = 'group_id,group_name';

        $data = GroupService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\listParam")
     * @Apidoc\Param("group_id", require=false, default=" ")
     * @Apidoc\Param("file_type", require=false, default=" ")
     * @Apidoc\Param("is_disable", require=false, default=" ")
     * @Apidoc\Param("is_front", require=false, default=" ")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="文件列表", 
     *     @Apidoc\Returned(ref="app\common\model\file\FileModel\listReturn")
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
        $group_id     = Request::param('group_id/s', '');
        $file_type    = Request::param('file_type/s', '');
        $is_disable   = Request::param('is_disable/s', '');
        $is_front     = Request::param('is_front/s', 0);

        if ($search_field && $search_value) {
            if ($search_field == 'file_id') {
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_disable != '') {
            $where[] = ['is_disable', '=', $is_disable];
        }
        if ($is_front != '') {
            $where[] = ['is_front', '=', $is_front];
        }
        $where[] = ['is_delete', '=', 0];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = FileService::list($where, $page, $limit, $order);

        $data['filetype'] = FileService::fileType();
        $data['storage']  = FileService::storage();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件信息")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\id")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\infoReturn")
     */
    public function info()
    {
        $param['file_id'] = Request::param('file_id/d', '');

        validate(FileValidate::class)->scene('info')->check($param);

        $data = FileService::info($param['file_id']);
        if ($data['is_delete'] == 1) {
            exception('文件已被删除：' . $param['file_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("文件添加")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function add()
    {
        $param['file']      = Request::file('file');
        $param['group_id']  = Request::param('group_id/d', 0);
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front']  = Request::param('is_front/s', 0);
        $param['sort']      = Request::param('sort/d', 250);

        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("文件修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\editParam")
     */
    public function edit()
    {
        $param['file_id']   = Request::param('file_id/d', '');
        $param['group_id']  = Request::param('group_id/d', 0);
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front']  = Request::param('is_front/s', 0);
        $param['sort']      = Request::param('sort/d', 250);

        validate(FileValidate::class)->scene('edit')->check($param);

        $data = FileService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_ids")
     */
    public function dele()
    {
        $param['file_ids'] = Request::param('file_ids/a', '');

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::dele($param['file_ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_ids")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\is_disable")
     */
    public function disable()
    {
        $param['file_ids']   = Request::param('file_ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(FileValidate::class)->scene('disable')->check($param);

        $data = FileService::disable($param['file_ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件分组")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_ids")
     * @Apidoc\Param(ref="app\common\model\file\GroupModel\id")
     */
    public function grouping()
    {
        $param['file_ids'] = Request::param('file_ids/a', '');
        $param['group_id'] = Request::param('group_id/d', 0);

        validate(FileValidate::class)->scene('grouping')->check($param);

        $data = FileService::group($param['file_ids'], $param['group_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="文件列表", 
     *     @Apidoc\Returned(ref="app\common\model\file\FileModel\listReturn")
     * )
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
        $date_value   = Request::param('date_value/a', '');
        $group_id     = Request::param('group_id/s', '');
        $file_type    = Request::param('file_type/s', '');
        $is_disable   = Request::param('is_disable/s', '');
        $is_front     = Request::param('is_front/s', '');

        if ($search_field && $search_value) {
            if ($search_field == 'file_id') {
                $where[] = [$search_field, '=', $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }
        if ($group_id) {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_disable != '') {
            $where[] = ['is_disable', '=', $is_disable];
        }
        if ($is_front != '') {
            $where[] = ['is_front', '=', $is_front];
        }
        $where[] = ['is_delete', '=', 1];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = FileService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_ids")
     */
    public function recoverReco()
    {
        $param['file_ids'] = Request::param('file_ids/a', '');

        validate(FileValidate::class)->scene('reco_reco')->check($param);

        $data = FileService::recoverReco($param['file_ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_ids")
     */
    public function recoverDele()
    {
        $param['file_ids'] = Request::param('file_ids/a', '');

        validate(FileValidate::class)->scene('reco_dele')->check($param);

        $data = FileService::recoverDele($param['file_ids']);

        return success($data);
    }
}
