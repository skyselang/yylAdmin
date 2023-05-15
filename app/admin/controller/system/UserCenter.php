<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\validate\system\UserCenterValidate;
use app\common\service\system\UserCenterService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("个人中心")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("800")
 */
class UserCenter extends BaseController
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Returned(ref="app\common\model\system\UserModel", withoutField="password")
     */
    public function info()
    {
        $param['user_id'] = user_id(true);

        validate(UserCenterValidate::class)->scene('info')->check($param);

        $data = UserCenterService::info($param['user_id']);
        if ($data['is_delete'] == 1) {
            exception('账号已被删除！');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("修改信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="avatar_id,nickname,username,phone,email")
     */
    public function edit()
    {
        $param = $this->params([
            'avatar_id/d' => 0,
            'nickname/s'  => '',
            'username/s'  => '',
            'phone/s'     => '',
            'email/s'     => '',
        ]);
        $param['user_id'] = user_id(true);

        validate(UserCenterValidate::class)->scene('edit')->check($param);

        $data = UserCenterService::edit($param['user_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("password_old", type="string", require=true, desc="旧密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
     */
    public function pwd()
    {
        $param = $this->params([
            'password_old/s' => '',
            'password_new/s' => '',
        ]);
        $param['user_id'] = user_id(true);

        validate(UserCenterValidate::class)->scene('pwd')->check($param);

        $data = UserCenterService::pwd($param['user_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("我的日志")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="日志列表", children={
     *   @Apidoc\Returned(ref="app\common\model\system\UserLogModel"),
     *   @Apidoc\Returned(ref="app\common\model\system\MenuModel", field="menu_name,menu_url")
     * })
     */
    public function log()
    {
        $param['user_id'] = user_id(true);

        validate(UserCenterValidate::class)->scene('log')->check($param);

        $where = $this->where(where_delete(['user_id', '=', $param['user_id']]));

        $data = UserCenterService::log($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }
}
