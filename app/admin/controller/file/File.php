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
use app\common\service\file\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件管理")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("410")
 */
class File
{
    /**
     * @Apidoc\Title("文件列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\listParam")
     * @Apidoc\Param("group_id", require=false, default="")
     * @Apidoc\Param("file_type", require=false, default="")
     * @Apidoc\Param("is_disable", require=false, default="")
     * @Apidoc\Param("is_front", require=false, default="0")
     * @Apidoc\Param("storage", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\FileModel\listReturn", type="array", desc="文件列表")
     * @Apidoc\Returned("group", ref="app\common\model\file\GroupModel\listReturn", type="array", desc="分组列表")
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
        $storage      = Request::param('storage/s', '');
        $file_type    = Request::param('file_type/s', '');
        $is_front     = Request::param('is_front/s', 0);
        $is_disable   = Request::param('is_disable/s', '');

        if ($search_field && $search_value) {
            if ($search_field == 'file_id') {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($storage != '') {
            $where[] = ['storage', '=', $storage];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_front != '') {
            $where[] = ['is_front', '=', $is_front];
        }
        if ($is_disable != '') {
            $where[] = ['is_disable', '=', $is_disable];
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

        $data = FileService::list($where, $page, $limit, $order);
        $data['storage'] = SettingService::storage();
        $data['filetype'] = SettingService::fileType();
        $data['group'] = GroupService::list([['is_disable', '=', 0], ['is_delete', '=', 0]], 1, 9999, [], 'group_id,group_name')['list'];

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

        return success($data);
    }

    /**
     * @Apidoc\Title("文件添加")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\addParam")
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
        $param['domain']    = Request::param('domain/s', '');
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front']  = Request::param('is_front/s', 0);
        $param['sort']      = Request::param('sort/d', 250);

        validate(FileValidate::class)->scene('edit')->check($param);

        $data = FileService::edit([$param['file_id']], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改分组")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\group_id")
     */
    public function editgroup()
    {
        $param['ids']      = Request::param('ids/a', '');
        $param['group_id'] = Request::param('group_id/d', 0);

        validate(FileValidate::class)->scene('editgroup')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改类型")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\file_type")
     */
    public function edittype()
    {
        $param['ids']       = Request::param('ids/a', '');
        $param['file_type'] = Request::param('file_type/s', 'image');

        validate(FileValidate::class)->scene('edittype')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改域名")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\domain")
     */
    public function editdomain()
    {
        $param['ids']    = Request::param('ids/a', '');
        $param['domain'] = Request::param('domain/s', '');

        validate(FileValidate::class)->scene('editdomain')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(FileValidate::class)->scene('disable')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\listParam")
     * @Apidoc\Param("group_id", require=false, default="")
     * @Apidoc\Param("file_type", require=false, default="")
     * @Apidoc\Param("is_disable", require=false, default="")
     * @Apidoc\Param("is_front", require=false, default="0")
     * @Apidoc\Param("storage", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\FileModel\listReturn", type="array", desc="文件列表")
     * @Apidoc\Returned("group", ref="app\common\model\file\GroupModel\listReturn", type="array", desc="分组列表")
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
        $storage      = Request::param('storage/s', '');
        $file_type    = Request::param('file_type/s', '');
        $is_front     = Request::param('is_front/s', '');
        $is_disable   = Request::param('is_disable/s', '');

        if ($search_field && $search_value) {
            if ($search_field == 'file_id') {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($storage != '') {
            $where[] = ['storage', '=', $storage];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_front != '') {
            $where[] = ['is_front', '=', $is_front];
        }
        if ($is_disable != '') {
            $where[] = ['is_disable', '=', $is_disable];
        }
        $where[] = ['is_delete', '=', 1];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = ['delete_time' => 'desc', 'is_disable' => 'desc', 'update_time' => 'desc'];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = FileService::list($where, $page, $limit, $order);
        $data['storage'] = SettingService::storage();
        $data['filetype'] = SettingService::fileType();
        $data['group'] = GroupService::list([['is_disable', '=', 0], ['is_delete', '=', 0]], 1, 9999, [], 'group_id,group_name')['list'];

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('reco')->check($param);

        $data = FileService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::dele($param['ids'], true);

        return success($data);
    }
}
