<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$controller.namespace};

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use {$validate.namespace}\{$validate.class_name} as Validate;
use {$service.namespace}\{$service.class_name} as Service;
use {$tables[0].namespace}\{$tables[0].model_name} as Model;

/**
 * @Apidoc\Title("lang({$form.controller_title})")
 * @Apidoc\Group("{$form.group}")
 * @Apidoc\Sort("250")
 */
class {$controller.class_name} extends BaseController
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
    * @Apidoc\Title("lang({$form.controller_title}列表)")
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
    * @Apidoc\Title("lang({$form.controller_title}信息)")
    * @Apidoc\Query(ref={Service::class,"info"})
    * @Apidoc\Returned(ref={Service::class,"info"})
    * @Apidoc\Returned(ref={Service::class,"basedata"})
    */
    public function info()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '']);

        validate($this->validate)->scene('info')->check($param);

        $data = $this->service::info($param[$pk]);
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
    * @Apidoc\Title("lang({$form.controller_title}添加)")
    * @Apidoc\Desc("lang(get获取基础数据，post提交添加)")
    * @Apidoc\Method("GET,POST")
    * @Apidoc\Param(ref={Service::class,"add"})
    * @Apidoc\Returned(ref={Service::class,"basedata"})
    */
    public function add()
    {
        if ($this->request->isGet()) {
            $data['basedata'] = $this->service::basedata();
            return success($data);
        }
        
        $param = $this->params($this->service::$editField);

        validate($this->validate)->scene('add')->check($param);

        $data = $this->service::add($param);

        return success($data);
    }

    /**
    * @Apidoc\Title("lang({$form.controller_title}修改)")
    * @Apidoc\Method("POST")
    * @Apidoc\Param(ref={Service::class,"edit"})
    */
    public function edit()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params($this->service::$editField);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param[$pk], $param);

        return success($data);
    }

    /**
    * @Apidoc\Title("lang({$form.controller_title}删除)")
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
     * @Apidoc\Title("lang({$form.controller_title}禁用)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
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
     * @Apidoc\Title("lang({$form.controller_title}批量修改)")
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
     * @Apidoc\Title("lang({$form.controller_title}导出)")
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

        $param['remark'] = $this->param('remark/s');
        $param['param']  = ['where' => $this->where(where_delete()), 'order' => $this->order()];

        $data = $this->service::export($param);

        return success($data);
    }
}
