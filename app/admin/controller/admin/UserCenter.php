<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\UserCenterValidate;
use app\common\service\admin\UserCenterService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("个人中心")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("650")
 */
class UserCenter extends BaseController
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\infoReturn")
     */
    public function info()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserCenterValidate::class)->scene('info')->check($param);

        $data = UserCenterService::info($param['admin_user_id']);
        if ($data['is_delete'] == 1) {
            exception('账号已被删除！');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("修改信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\editParam")
     */
    public function edit()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['avatar_id']     = $this->param('avatar_id/d', 0);
        $param['username']      = $this->param('username/s', '');
        $param['nickname']      = $this->param('nickname/s', '');
        $param['phone']         = $this->param('phone/s', '');
        $param['email']         = $this->param('email/s', '');

        validate(UserCenterValidate::class)->scene('edit')->check($param);

        $data = UserCenterService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
     */
    public function pwd()
    {
        $param['admin_user_id'] = admin_user_id();
        $param['password_old']  = $this->param('password_old/s', '');
        $param['password_new']  = $this->param('password_new/s', '');

        validate(UserCenterValidate::class)->scene('pwd')->check($param);

        $data = UserCenterService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("我的日志")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\listParam")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Param("response_code", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\admin\UserLogModel\listReturn", type="array", desc="日志列表")
     */
    public function log()
    {
        $admin_user_id = admin_user_id();
        $log_type      = $this->param('log_type/d', '');

        validate(UserCenterValidate::class)->scene('log')->check(['admin_user_id' => $admin_user_id]);

        $where[] = ['admin_user_id', '=', $admin_user_id];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        $where = $this->where($where, 'admin_user_log_id,admin_menu_id,menu_url,menu_name');

        $data = UserCenterService::log($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }
}
