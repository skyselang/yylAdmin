<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\file;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\file\FileValidate as Validate;
use app\common\service\file\FileService as Service;
use app\common\model\file\FileModel as Model;
use app\common\service\file\SettingService;

/**
 * @Apidoc\Title("lang(文件管理)")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("100")
 */
class File extends BaseController
{
    /**
     * 验证器
     */
    protected $validate = Validate::class;

    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    /**
     * @Apidoc\Title("lang(文件列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function list()
    {
        $where = $this->where(where_delete());
        $param = $this->param();

        $data = $this->service::list($where, $this->page(), $this->limit(), $this->order(), '', true, $param);
        $data['basedata'] = $this->service::basedata(true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件信息)")
     * @Apidoc\Query(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Query("is_down", type="int", desc="lang(是否下载文件，1是，0否)")
     */
    public function info()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '', 'is_down/d' => 0]);

        validate($this->validate)->scene('info')->check($param);

        $data = $this->service::info($param[$pk]);
        $data['basedata'] = $this->service::basedata();

        if ($param['is_down'] && $data['add_type'] === 'upload' && ($data['storage'] ?? '') === 'local') {
            return download($data['file_path'], $data['file_name']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件添加)")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref={Service::class,"add"})
     * @Apidoc\Returned(ref={Service::class,"add"})
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $data['basedata'] = $this->service::basedata();
            return success($data);
        }

        $setting = SettingService::info();
        if (!$setting['is_upload_admin']) {
            exception(lang('文件上传未开启，无法上传文件！'));
        }

        $add_type = $this->param('add_type/s', 'upload');
        if ($add_type === 'add') {
            $editField = [
                'add_type/s'  => 'add',
                'unique/s'    => '',
                'file_name/s' => '',
                'group_id/d'  => 0,
                'tag_ids/a'   => [],
                'file_type/s' => 'image',
                'file_url/s'  => '',
                'remark/s'    => '',
                'sort/d'      => 250,
            ];
            $params = $this->params($editField);

            $files = $data = [];
            $file_urls = trim($params['file_url'], ',');
            $file_urls = explode(',', $file_urls);
            foreach ($file_urls as $file_url) {
                $param = $params;
                $param['file_url'] = trim($file_url);
                validate($this->validate)->scene('addurl')->check($param);
                $files[] = $param;
            }
            foreach ($files as $k => $file) {
                if ($k > 0 && ($file['unique'] ?? '')) {
                    $file['unique'] .= $k; 
                }
                $data[] = $this->service::add($file);
            }
            if (count($data) === 1) {
                $data = $data[0];
            }
            return success($data, lang('添加成功'));
        } else {
            $param['file']     = $this->request->file('file');
            $param['add_type'] = 'upload';
            if (request()->has('group_id')) {
                $param['group_id'] = $this->param('group_id');
            }
            if (request()->has('tag_ids')) {
                $param['tag_ids'] = $this->param('tag_ids');
            }

            validate($this->validate)->scene('add')->check($param);

            $data = $this->service::add($param);
            return success($data, lang('上传成功'));
        }
    }

    /**
     * @Apidoc\Title("lang(文件修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"})
     */
    public function edit()
    {
        $pk = $this->model()->getPk();

        if ($this->request->isGet()) {
            $param = $this->params([$pk => '']);

            validate($this->validate)->scene('info')->check($param);

            $data = $this->service::info($param[$pk]);
            $data['basedata'] = $this->service::basedata();

            return success($data);
        }

        $param = $this->params($this->service::$editField);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param[$pk], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件删除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"dele"})
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate($this->validate)->scene('dele')->check($param);

        $data = $this->service::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件是否禁用)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate($this->validate)->scene('disable')->check($param);

        $data = $this->service::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件批量修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"update"})
     */
    public function update()
    {
        $param = $this->params(['ids/a' => [], 'field/s' => '', 'value']);

        validate($this->validate)->scene('update')->check($param);

        $data = $this->service::update($param['ids'], $param['field'], $param['value']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件导出)")
     * @Apidoc\Desc("lang(post提交导出，get下载导出文件)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Query(ref={Service::class,"export"})
     * @Apidoc\Param(ref={Service::class,"export"})
     * @Apidoc\Returned(ref={Service::class,"export"})
     */
    public function export()
    {
        if ($this->request->isGet()) {
            $param = $this->params(['file_path/s' => '', 'file_name/s' => '']);
            return download($param['file_path'], $param['file_name']);
        }

        $recycle = $this->param('recycle/d', 0);
        $ids     = $this->param('ids/a', []);
        $where   = [];
        if ($ids) {
            $model = $this->model();
            $pk    = $model->getPk();
            $where = [$pk, 'in', $ids];
        }
        $param['remark'] = $this->param('remark/s');
        $param['param']  = ['where' => $this->where(where_delete($where, $recycle)), 'order' => $this->order()];

        $data = $this->service::export($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件导入)")
     * @Apidoc\Desc("lang(get下载导入模板，post提交导入文件)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Query(ref={Service::class,"import"})
     * @Apidoc\Param(ref={Service::class,"import"})
     * @Apidoc\Returned(ref={Service::class,"import"})
     */
    public function import()
    {
        if ($this->request->isGet()) {
            $param = $this->params(['file_path/s' => '', 'file_name/s' => '']);
            if ($param['file_path']) {
                return download($param['file_path'], $param['file_name']);
            } else {
                $data = $this->service::export(['is_import' => 1, 'param' => ['where' => [where_delete()]]]);
                return success($data);
            }
        }

        $param['import_file'] = $this->request->file('import_file');
        $param['is_update']   = $this->param('is_update/d', 0);
        $param['remark']      = $this->param('remark/s');

        validate($this->validate)->scene('import')->check($param);

        $data = $this->service::import($param, true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件回收站列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function recycleList()
    {
        $pk    = $this->model()->getPk();
        $where = $this->where(where_delete([], 1));
        $order = ['delete_time' => 'desc', $pk => 'desc'];
        $param = $this->param();

        $data = $this->service::list($where, $this->page(), $this->limit(), $this->order($order), '', true, $param);
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件回收站恢复)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleReco()
    {
        $param = $this->params(['ids/a' => []]);

        validate($this->validate)->scene('recycleReco')->check($param);

        $data = $this->service::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件回收站删除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recycleDele()
    {
        $param = $this->params(['ids/a' => []]);

        validate($this->validate)->scene('recycleDele')->check($param);

        $data = $this->service::dele($param['ids'], true);

        return success($data);
    }
}
