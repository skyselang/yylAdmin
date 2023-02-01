<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\setting\SettingValidate;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("700")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel")
     */
    public function edit()
    {
        $param['favicon_id']  = $this->request->param('favicon_id/d', 0);
        $param['logo_id']     = $this->request->param('logo_id/d', 0);
        $param['name']        = $this->request->param('name/s', '');
        $param['title']       = $this->request->param('title/s', '');
        $param['keywords']    = $this->request->param('keywords/s', '');
        $param['description'] = $this->request->param('description/s', '');
        $param['icp']         = $this->request->param('icp/s', '');
        $param['copyright']   = $this->request->param('copyright/s', '');
        $param['offi_id']     = $this->request->param('offi_id/d', 0);
        $param['mini_id']     = $this->request->param('mini_id/d', 0);
        $param['address']     = $this->request->param('address/s', '');
        $param['tel']         = $this->request->param('tel/s', '');
        $param['fax']         = $this->request->param('fax/s', '');
        $param['mobile']      = $this->request->param('mobile/s', '');
        $param['email']       = $this->request->param('email/s', '');
        $param['qq']          = $this->request->param('qq/s', '');
        $param['wechat']      = $this->request->param('wechat/s', '');
        $param['is_feedback'] = $this->request->param('is_feedback/d', 1);
        $param['diy_config']  = $this->request->param('diy_config/a', []);

        validate(SettingValidate::class)->scene('edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
