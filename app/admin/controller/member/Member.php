<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate as Validate;
use app\common\service\member\MemberService as Service;
use app\common\model\member\MemberModel as Model;
use app\common\service\member\ThirdService;
use app\common\service\member\SettingService;

/**
 * @Apidoc\Title("lang(会员管理)")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("100")
 */
class Member extends BaseController
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
     * @Apidoc\Title("lang(会员列表)")
     * @Apidoc\Query(ref={Service::class,"list"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"list"})
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = $this->service::list($where, $this->page(), $this->limit(), $this->order());
        $data['basedata'] = $this->param('basedata/d', 1) ? $this->service::basedata(true) : [];

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员信息)")
     * @Apidoc\Query(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"info"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function info()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([$pk => '']);

        validate($this->validate)->scene('info')->check($param);

        $data = $this->service::info($param[$pk], true, true, true);
        $data['basedata'] = $this->service::basedata();
        unset($data['password']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员添加)")
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
        $param['password']    = $this->param('password', '');
        $param['platform']    = SettingService::PLATFORM_YA;
        $param['application'] = SettingService::APP_YA_ADMIN;

        validate($this->validate)->scene('add')->check($param);

        $data = $this->service::add($param);
        unset($data['password']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员修改)")
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

            $data = $this->service::info($param[$pk], true, true, true);
            $data['basedata'] = $this->service::basedata();
            unset($data['password']);

            return success($data);
        }

        $param = $this->params($this->service::$editField);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param[$pk], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员删除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"dele"})
     */
    public function dele()
    {
        $type  = $this->param('type', 'member');
        $param = $this->params(['ids']);

        validate($this->validate)->scene('dele')->check($param);

        if ($type == 'third') {
            $data = $this->service::thirdUnbind($param['ids'][0]);
        } else {
            $data = $this->service::dele($param['ids']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员是否禁用)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"disable"})
     */
    public function disable()
    {
        $type  = $this->param('type', 'member');
        $param = $this->params(['ids', 'is_disable/d' => 0]);

        validate($this->validate)->scene('disable')->check($param);

        if ($type == 'third') {
            $data = ThirdService::edit($param['ids'], $param);
        } else {
            $data = $this->service::disable($param['ids'], $param['is_disable']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员批量修改)")
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
     * @Apidoc\Title("lang(会员修改密码)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editpwd"})
     */
    public function editPwd()
    {
        $param = $this->params(['ids/a' => [], 'password/s' => '']);

        validate($this->validate)->scene('editpwd')->check($param);

        $data = $this->service::editpwd($param['ids'], $param['password']);
        unset($data['password']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员修改分组)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editgroup"})
     */
    public function editGroup()
    {
        $param = $this->params(['ids/a' => [], 'group_ids/a' => []]);

        validate($this->validate)->scene('editgroup')->check($param);

        $data = $this->service::editgroup($param['ids'], $param['group_ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员修改超会)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"editsuper"})
     */
    public function editSuper()
    {
        $param = $this->params(['ids/a' => [], 'is_super/d' => 0]);

        validate($this->validate)->scene('editsuper')->check($param);

        $data = $this->service::editsuper($param['ids'], $param['is_super']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员导出)")
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
     * @Apidoc\Title("lang(会员导入)")
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
}
