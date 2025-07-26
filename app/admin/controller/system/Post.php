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
use app\common\validate\system\PostValidate as Validate;
use app\common\service\system\PostService as Service;
use app\common\model\system\PostModel as Model;
use app\common\service\system\UserService;

/**
 * @Apidoc\Title("lang(职位管理)")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("300")
 */
class Post extends BaseController
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
     * @Apidoc\Title("lang(职位列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function list()
    {
        $where  = $this->where(where_delete());
        $order  = $this->order();
        $islist = $this->param('islist');
        $param  = ['islist' => $islist, 'search_mode' => $this->param('search_mode')];

        $basedata = $this->service::basedata(true);
        if ($islist) {
            $data['list']  = $this->service::list('list', $where, $order, '', 0, 0, $param);
            $data['count'] = count($data['list']);
        } else {
            $data['list']  = $this->service::list('tree', $where, $order);
            $data['count'] = count($this->service::list('list', $where, $order));
            if (count($where) > 1) {
                $list = tree_to_list($data['list']);
                $all  = tree_to_list($basedata['trees']);
                $pk   = $this->model()->getPk();
                $pid  = $this->model()->pidk;
                $ids  = [];
                foreach ($list as $val) {
                    $pids = children_parent_key($all, $val[$pk], $pk, $pid);
                    $cids = parent_children_key($all, $val[$pk], $pk, $pid);
                    $ids  = array_merge($ids, $pids, $cids);
                }
                $data['list'] = $this->service::list('tree', [[$pk, 'in', $ids], where_delete()], $order);
            }
        }
        $data['basedata'] = $basedata;

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(职位信息)")
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
     * @Apidoc\Title("lang(职位添加)")
     * @Apidoc\Desc("lang(get获取基础数据，post提交添加)")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Param(ref={Service::class,"add"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $data['basedata'] = $this->service::basedata();
            return success($data);
        }

        $pk    = $this->model()->getPk();
        $param = $this->params($this->service::$editField);
        unset($param[$pk]);

        validate($this->validate)->scene('add')->check($param);

        $data = $this->service::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(职位修改)")
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
     * @Apidoc\Title("lang(职位删除)")
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
     * @Apidoc\Title("lang(职位是否禁用)")
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
     * @Apidoc\Title("lang(职位批量修改)")
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
     * @Apidoc\Title("lang(职位导出)")
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

    /**
     * @Apidoc\Title("lang(职位用户列表)")
     * @Apidoc\Query(ref={Service::class,"userList"})
     * @Apidoc\Returned(ref={UserService::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"userList"})
     */
    public function userList()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '']);

        validate($this->validate)->scene('userList')->check($param);

        $where = $this->where(where_delete([$pk, '=', $param[$pk]]));

        $data = $this->service::userList($where, $this->page(), $this->limit(), $this->order());
        $data['basedata'] = UserService::basedata(true);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(职位用户解除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"userLift"})
     */
    public function userLift()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => [], 'user_ids/a' => []]);

        validate($this->validate)->scene('userLift')->check($param);

        $data = $this->service::userLift($param[$pk], $param['user_ids']);

        return success($data);
    }
}
