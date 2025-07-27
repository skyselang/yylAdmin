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
use app\common\validate\file\ImportValidate as Validate;
use app\common\service\file\ImportService as Service;
use app\common\model\file\ImportModel as Model;

/**
 * @Apidoc\Title("lang(导入文件)")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("300")
 */
class Import extends BaseController
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
     * @Apidoc\Title("lang(导入文件列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = $this->service::list($where, $this->page(), $this->limit(), $this->order());
        $data['basedata'] = $this->service::basedata(true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(导入文件信息)")
     * @Apidoc\Query(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Query("is_down", type="int", desc="lang(是否下载文件，1是，0否)")
     * @Apidoc\Query("file_type", type="string", desc="lang(下载文件类型，import导入文件，success成功文件，fail失败文件)")
     */
    public function info()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '', 'is_down/d' => 0, 'file_type/s' => 'import']);

        validate($this->validate)->scene('info')->check($param);

        $data = $this->service::info($param[$pk]);
        $data['basedata'] = $this->service::basedata();

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
     * @Apidoc\Title("lang(导入文件修改)")
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
     * @Apidoc\Title("lang(导入文件删除)")
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
     * @Apidoc\Title("lang(导入文件批量修改)")
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
     * @Apidoc\Title("lang(导入文件是否禁用)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"disable"})
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate($this->validate)->scene('disable')->check($param);

        $data = $this->service::disable($param['ids'], $param['is_disable']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(导入文件导出)")
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
     * @Apidoc\Title("lang(导入文件回收站列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function recycleList()
    {
        $pk    = $this->model()->getPk();
        $where = $this->where(where_delete([], 1));
        $order = $this->order(['delete_time' => 'desc', $pk => 'desc']);

        $data = $this->service::list($where, $this->page(), $this->limit(), $order);
        $data['basedata'] = $this->service::basedata(true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(导入文件回收站恢复)")
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
     * @Apidoc\Title("lang(导入文件回收站删除)")
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
