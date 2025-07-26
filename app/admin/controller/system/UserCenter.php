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
use app\common\validate\system\UserCenterValidate as Validate;
use app\common\service\system\UserCenterService as Service;
use app\common\model\system\UserModel as Model;
use app\common\validate\system\UserLogValidate;
use app\common\service\system\SettingService;

/**
 * @Apidoc\Title("lang(个人中心)")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("600")
 */
class UserCenter extends BaseController
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
     * @Apidoc\Title("lang(我的信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, withoutField="password")
     * @Apidoc\Returned("setting",type="object", desc="lang(系统设置)",ref={SettingService::class,"info"},field="system_name,page_title,favicon_url,logo_url,login_bg_url,login_bg_color,page_limit,is_watermark,api_timeout,token_type,token_name,captcha_switch,captcha_mode,captcha_type")
     */
    public function info()
    {
        $pk = $this->model()->getPk();
        $param[$pk] = user_id(true);

        validate($this->validate)->scene('info')->check($param);

        $data = $this->service::info($param[$pk]);
        if ($data['is_delete'] == 1) {
            exception(lang('账号已被删除'));
        }

        $data['setting'] = SettingService::info('system_name,page_title,favicon_url,logo_url,login_bg_url,login_bg_color,page_limit,is_watermark,api_timeout,token_type,token_name,captcha_switch,captcha_mode,captcha_type');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(我的信息修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="avatar_id,nickname,username,phone,email")
     */
    public function edit()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params([
            'avatar_id/d' => 0,
            'nickname/s'  => '',
            'username/s'  => '',
            'phone/s'     => '',
            'email/s'     => '',
        ]);
        $param[$pk] = user_id(true);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param[$pk], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(我的密码修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"pwd"})
     */
    public function pwd()
    {
        $pk    = $this->model()->getPk();
        $param = $this->params(['password_old/s' => '', 'password_new/s' => '']);
        $param[$pk] = user_id(true);

        validate($this->validate)->scene('pwd')->check($param);

        $this->service::pwd($param[$pk], $param);

        return success();
    }

    /**
     * @Apidoc\Title("lang(我的日志列表)")
     * @Apidoc\Query(ref={Service::class,"logList"})
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     * @Apidoc\Returned(ref={Service::class,"logList"})
     */
    public function logList()
    {
        $pk = $this->model()->getPk();
        $param[$pk] = user_id(true);

        validate($this->validate)->scene('log')->check($param);

        $where = $this->where(where_disable([$pk, '=', $param[$pk]]));

        $data = $this->service::logList($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(我的日志信息)")
     * @Apidoc\Query(ref={Service::class,"logInfo"})
     * @Apidoc\Returned(ref={Service::class,"logInfo"})
     */
    public function logInfo()
    {
        $pk      = $this->model()->getPk();
        $param   = $this->params(['log_id' => '']);
        $user_id = user_id(true);

        validate(UserLogValidate::class)->scene('info')->check($param);

        $data = $this->service::logInfo($param['log_id']);
        if ($data[$pk] != $user_id) {
            $data = [];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(我的日志删除)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"logDele"})
     */
    public function logDele()
    {
        $param   = $this->params(['ids/a' => []]);
        $user_id = user_id(true);

        validate(UserLogValidate::class)->scene('dele')->check($param);

        $data = $this->service::logDele($param['ids'], $user_id);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(我的日志清空)")
     * @Apidoc\Method("POST")
     * @Apidoc\Query(ref={Service::class,"logClear"})
     */
    public function logClear()
    {
        $where = $this->where(['user_id', '=', user_id(true)]);

        $data = $this->service::logClear($where);

        return success($data);
    }
}
