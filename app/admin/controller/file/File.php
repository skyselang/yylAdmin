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
use app\common\validate\file\FileValidate;
use app\common\service\file\FileService;
use app\common\service\file\GroupService;
use app\common\service\file\TagService;
use app\common\service\file\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("文件管理")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("100")
 */
class File extends BaseController
{
    /**
     * @Apidoc\Title("文件列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Query(ref="app\common\model\file\FileModel", field="group_id,storage,file_type,is_front,is_disable")
     * @Apidoc\Query("tag_ids", type="array", desc="标签id")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="文件列表", children={
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel", field="file_id,unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_size,file_ext,sort,is_disable,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getGroupNameAttr", field="group_name"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getTagNamesAttr", field="tag_names"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getFileTypeNameAttr", field="file_type_name"),
     *   @Apidoc\Returned(ref="app\common\model\file\FileModel\getFileUrlAttr", field="file_url"),
     * })
     * @Apidoc\Returned("setting", ref="app\common\service\file\SettingService\info", type="object", desc="文件设置")
     * @Apidoc\Returned("group", ref="app\common\model\file\GroupModel", type="array", desc="分组列表", field="group_id,group_name")
     * @Apidoc\Returned("tag", ref="app\common\model\file\TagModel", type="array", desc="标签列表", field="tag_id,tag_name")
     */
    public function list()
    {
        $group_id   = $this->param('group_id/s', '');
        $tag_ids    = $this->param('tag_ids/a', []);
        $storage    = $this->param('storage/s', '');
        $file_type  = $this->param('file_type/s', '');
        $is_front   = $this->param('is_front/s', 0);
        $is_disable = $this->param('is_disable/s', '');

        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($tag_ids ?? []) {
            $where[] = ['tag_ids', 'in', $tag_ids];
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
        $where[] = where_delete();
        $where = $this->where($where);

        $data = FileService::list($where, $this->page(), $this->limit(), $this->order());

        $data['group'] = GroupService::list([where_delete()], 0, 0, [], 'group_id,group_name')['list'] ?? [];
        $data['tag']   = TagService::list([where_delete()], 0, 0, [], 'tag_id,tag_name')['list'] ?? [];
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("文件信息")
     * @Apidoc\Query(ref="app\common\model\file\FileModel", field="file_id")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\getGroupNameAttr", field="group_name")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\getTagIdsAttr", field="tag_ids")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\getTagNamesAttr", field="tag_names")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\getFileTypeNameAttr", field="file_type_name")
     * @Apidoc\Returned(ref="app\common\model\file\FileModel\getFileUrlAttr", field="file_url")
     * @Apidoc\Returned("tag_ids", type="array", desc="标签id")
     */
    public function info()
    {
        $param = $this->params(['file_id/d' => '']);

        validate(FileValidate::class)->scene('info')->check($param);

        $data = FileService::info($param['file_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件添加")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("type", type="string", default="upl", desc="url添加，upl上传")
     * @Apidoc\Param("file_url", type="string", require=true, desc="文件链接, 添加（type=url）时必传")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="文件, 上传（type=upl）时必传")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="group_id,file_type,remark,sort")
     * @Apidoc\Param("tag_ids", type="array", desc="标签id")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function add()
    {
        $setting = SettingService::info();
        if (!$setting['is_upload_admin']) {
            exception('文件上传未开启，无法上传文件！');
        }

        $type = $this->param('type/s', 'upl');
        if ($type == 'url') {
            $edit_field = [
                'type/s'      => 'url',
                'group_id/d'  => 0,
                'tag_ids/a'   => [],
                'file_type/s' => 'image',
                'file_url/s'  => '',
                'remark/s'    => '',
                'sort/d'      => 250,
            ];
            $params = $this->params($edit_field);
            if ($params['group_id'] === 0) {
                unset($params['group_id']);
            }
            if ($params['tag_ids'] === []) {
                unset($params['tag_ids']);
            }
            if ($params['remark'] === '') {
                unset($params['remark']);
            }

            $files = $data = [];
            $file_urls = trim($params['file_url'], ',');
            $file_urls = explode(',', $file_urls);
            foreach ($file_urls as $file_url) {
                $param = $params;
                $param['file_url'] = trim($file_url);
                validate(FileValidate::class)->scene('addurl')->check($param);
                $files[] = $param;
            }
            foreach ($files as $file) {
                $data[] = FileService::add($file);
            }
            if (count($data) == 1) {
                $data = $data[0];
            }
            return success($data, '添加成功');
        } else {
            $param['file'] = $this->request->file('file');

            validate(FileValidate::class)->scene('add')->check($param);

            $data = FileService::add($param);
            return success($data, '上传成功');
        }
    }

    /**
     * @Apidoc\Title("文件修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="file_id,file_name,group_id,file_type,domain,remark,sort")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\getTagIdsAttr", field="tag_ids")
     */
    public function edit()
    {
        $param = $this->params(FileService::$edit_field);

        validate(FileValidate::class)->scene('edit')->check($param);

        $data = FileService::edit($param['file_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(FileValidate::class)->scene('dele')->check($param);

        $data = FileService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改分组")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="group_id")
     */
    public function editgroup()
    {
        $param = $this->params(['ids/a' => [], 'group_id/d' => 0]);

        validate(FileValidate::class)->scene('editgroup')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改标签")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\getTagIdsAttr", field="tag_ids")
     */
    public function edittag()
    {
        $param = $this->params(['ids/a' => [], 'tag_ids/a' => []]);

        validate(FileValidate::class)->scene('edittag')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改类型")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="file_type")
     */
    public function edittype()
    {
        $param = $this->params(['ids/a' => [], 'file_type/s' => 'image']);

        validate(FileValidate::class)->scene('edittype')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件修改域名")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="domain")
     */
    public function editdomain()
    {
        $param = $this->params(['ids/a' => [], 'domain/s' => '']);

        validate(FileValidate::class)->scene('editdomain')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(FileValidate::class)->scene('disable')->check($param);

        $data = FileService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站")
     * @Apidoc\Desc("请求和返回参数同文件列表")
     */
    public function recycle()
    {
        $group_id   = $this->param('group_id/s', '');
        $tag_ids    = $this->param('tag_ids/a', []);
        $storage    = $this->param('storage/s', '');
        $file_type  = $this->param('file_type/s', '');
        $is_front   = $this->param('is_front/s', '');
        $is_disable = $this->param('is_disable/s', '');

        if ($group_id !== '') {
            $where[] = ['group_id', '=', $group_id];
        }
        if ($tag_ids ?? []) {
            $where[] = ['tag_ids', 'in', $tag_ids];
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
        $where[] = where_delete([], 1);
        $where = $this->where($where);

        $order = $this->order(['delete_time' => 'desc', 'file_id' => 'desc']);

        $data = FileService::list($where, $this->page(), $this->limit(), $order);

        $data['group'] = GroupService::list([where_delete()], 0, 0, [], 'group_id,group_name')['list'] ?? [];
        $data['tag']   = TagService::list([where_delete()], 0, 0, [], 'tag_id,tag_name')['list'] ?? [];
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleReco()
    {
        $param = $this->params(['ids/a' => []]);

        validate(FileValidate::class)->scene('recycleReco')->check($param);

        $data = FileService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("文件回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleDele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(FileValidate::class)->scene('recycleDele')->check($param);

        $data = FileService::dele($param['ids'], true);

        return success($data);
    }
}
