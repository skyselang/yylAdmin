<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use hg\apidoc\annotation as Apidoc;
use app\api\service\LoginService;
use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\service\member\MemberService;

/**
 * @Apidoc\Title("lang(退出)")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("210")
 */
class Logout extends BaseController
{
    /**
     * @Apidoc\Title("lang(退出)")
     * @Apidoc\Method("POST")
     * @Apidoc\Before(event="clearGlobalHeader", key="ApiToken")
     * @Apidoc\Before(event="clearGlobalQuery", key="ApiToken")
     * @Apidoc\Before(event="clearGlobalBody", key="ApiToken")
     */
    public function logout()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('logout')->check($param);

        $data = LoginService::logout($param['member_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(注销账号)")
     * @Apidoc\Desc("注销后会删除账号")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("password", type="string", require=true, desc="密码")
     */
    public function cancel()
    {
        $member_id = member_id(true);
        $param     = $this->params(['password/s' => '']);
        $rule      = ['password' => 'require'];
        $message   = ['password.require' => lang('密码不能为空')];
        $validate  = validate($rule, $message, false, false);
        if (!$validate->check($param)) {
            return error($validate->getError());
        }

        $data = MemberService::cancel($member_id, $param['password']);

        return success($data);
    }
}
