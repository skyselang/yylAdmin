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
use app\common\validate\file\FileValidate;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件管理")
 * @Apidoc\Group("adminFile")
 * @Apidoc\Sort("410")
 */
class File extends BaseController
{
    /**
     * @Apidoc\Title("文件列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\listParam")
     * @Apidoc\Param("group_id", require=false, default="")
     * @Apidoc\Param("storage", require=false, default="")
     * @Apidoc\Param("file_type", require=false, default="")
     * @Apidoc\Param("is_front", require=false, default="0")
     * @Apidoc\Param("is_disable", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\FileModel\listReturn", type="array", desc="文件列表", 
     *     @Apidoc\Returned(ref="app\common\model\file\FileModel\file_url")
     * )
     * @Apidoc\Returned("group", type="array", desc="分组列表",
     *     @Apidoc\Returned(ref="app\common\model\file\GroupModel\id"),
     *     @Apidoc\Returned(ref="app\common\model\file\GroupModel\group_name")
     * )
     * @Apidoc\Returned("storage", type="object", desc="存储方式")
     * @Apidoc\Returned("filetype", type="object", desc="文件类型")
     * @Apidoc\Returned("setting", type="object", desc="文件设置")
     */
    public function list()
    {
        $group_id   = $this->param('group_id/s', '');
        $storage    = $this->param('storage/s', '');
        $file_type  = $this->param('file_type/s', '');
        $is_front   = $this->param('is_front/s', 0);
        $is_disable = $this->param('is_disable/s', '');

        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($storage !== '') {
            $where[] = ['storage', '=', $storage];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_front !== '') {
            $where[] = ['is_front', '=', $is_front];
        }
        if ($is_disable !== '') {
            $where[] = ['is_disable', '=', $is_disable];
        }
        $where[] = ['is_delete', '=', 0];
        $where = $this->where($where, 'file_id');

        $data = FileService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("文件信息")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\id")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\infoReturn")
     */
    public function info()
    {
        $param['file_id'] = $this->param('file_id/d', '');

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
        $param['file']      = $this->request->file('file');
        $param['group_id']  = $this->param('group_id/d', 0);
        $param['file_type'] = $this->param('file_type/s', 'image');
        $param['file_name'] = $this->param('file_name/s', '');
        $param['is_front']  = $this->param('is_front/s', 0);
        $param['sort']      = $this->param('sort/d', 250);

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
        $param['file_id']   = $this->param('file_id/d', '');
        $param['group_id']  = $this->param('group_id/d', 0);
        $param['domain']    = $this->param('domain/s', '');
        $param['file_type'] = $this->param('file_type/s', 'image');
        $param['file_name'] = $this->param('file_name/s', '');
        $param['is_front']  = $this->param('is_front/s', 0);
        $param['sort']      = $this->param('sort/d', 250);

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
        $param['ids'] = $this->param('ids/a', '');

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
        $param['ids']      = $this->param('ids/a', '');
        $param['group_id'] = $this->param('group_id/d', 0);

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
        $param['ids']       = $this->param('ids/a', '');
        $param['file_type'] = $this->param('file_type/s', 'image');

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
        $param['ids']    = $this->param('ids/a', '');
        $param['domain'] = $this->param('domain/s', '');

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
        $param['ids']        = $this->param('ids/a', '');
        $param['is_disable'] = $this->param('is_disable/d', 0);

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
     * @Apidoc\Param("storage", require=false, default="")
     * @Apidoc\Param("file_type", require=false, default="")
     * @Apidoc\Param("is_front", require=false, default="0")
     * @Apidoc\Param("is_disable", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\file\FileModel\listReturn", type="array", desc="文件列表", 
     *     @Apidoc\Returned(ref="app\common\model\file\FileModel\file_url")
     * )
     * @Apidoc\Returned("group", type="array", desc="分组列表",
     *     @Apidoc\Returned(ref="app\common\model\file\GroupModel\id"),
     *     @Apidoc\Returned(ref="app\common\model\file\GroupModel\group_name")
     * )
     * @Apidoc\Returned("storage", type="object", desc="存储方式")
     * @Apidoc\Returned("filetype", type="object", desc="文件类型")
     * @Apidoc\Returned("setting", type="object", desc="文件设置")
     */
    public function recover()
    {
        $group_id   = $this->param('group_id/s', '');
        $storage    = $this->param('storage/s', '');
        $file_type  = $this->param('file_type/s', '');
        $is_front   = $this->param('is_front/s', '');
        $is_disable = $this->param('is_disable/s', '');

        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($storage !== '') {
            $where[] = ['storage', '=', $storage];
        }
        if ($file_type) {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($is_front !== '') {
            $where[] = ['is_front', '=', $is_front];
        }
        if ($is_disable !== '') {
            $where[] = ['is_disable', '=', $is_disable];
        }
        $where[] = ['is_delete', '=', 1];
        $where = $this->where($where, 'file_id');

        $order = ['delete_time' => 'desc', 'is_disable' => 'desc', 'update_time' => 'desc'];

        $data = FileService::list($where, $this->page(), $this->limit(), $this->order($order));

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = $this->param('ids/a', '');

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
        $param['ids'] = $this->param('ids/a', '');

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::dele($param['ids'], true);

        return success($data);
    }
}
