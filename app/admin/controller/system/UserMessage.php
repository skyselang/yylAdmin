<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\system\UserMessageValidate as Validate;
use app\common\service\system\UserMessageService as Service;
use app\common\model\system\UserMessageModel as Model;
use app\common\service\system\MessageService;

/**
 * @Apidoc\Title("lang(用户消息)")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("370")
 */
class UserMessage extends BaseController
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
     * @Apidoc\Title("lang(用户消息列表)")
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
     * @Apidoc\Title("lang(用户消息信息)")
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
     * @Apidoc\Title("lang(用户消息添加)")
     * @Apidoc\Desc("lang(get获取基础数据，post提交添加)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Param(ref={Service::class,"add"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $message_id = $this->param('message_id/d');
            if ($message_id) {
                $data = MessageService::info($message_id, false);
                return success($data);
            }

            $data['basedata'] = $this->service::basedata();

            return success($data);
        }

        $pk    = $this->model()->getPk();
        $param = $this->params($this->service::$editField);
        unset($param[$pk]);

        validate($this->validate)->scene('add')->check($param);

        $data = $this->service::add($param, false);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(用户消息修改)")
     * @Apidoc\Desc("lang(get获取数据，post提交修改)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Query(ref={Service::class,"info"})
     * @Apidoc\Param(ref={Service::class,"edit"})
     * @Apidoc\Returned(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function edit()
    {
        $pk = $this->model()->getPk();

        if ($this->request->isGet()) {
            $message_id = $this->param('message_id/d');
            if ($message_id) {
                $data = MessageService::info($message_id, false);
                return success($data);
            }

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
     * @Apidoc\Title("lang(用户消息删除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"dele"})
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate($this->validate)->scene('dele')->check($param);

        $data = $this->service::dele($param['ids'], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(用户消息是否禁用)")
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
     * @Apidoc\Title("lang(用户消息批量修改)")
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
     * @Apidoc\Title("lang(用户消息导出)")
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

        $ids   = $this->param('ids/a', []);
        $where = [];
        if ($ids) {
            $model = $this->model();
            $pk    = $model->getPk();
            $where = [$pk, 'in', $ids];
        }
        $param['remark'] = $this->param('remark/s');
        $param['param']  = ['where' => $this->where(where_delete($where)), 'order' => $this->order()];

        $data = $this->service::export($param);

        return success($data);
    }
}
