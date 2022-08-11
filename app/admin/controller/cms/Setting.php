<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\cms;

use app\common\BaseController;
use app\common\validate\cms\SettingValidate;
use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容设置")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("340")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("内容设置信息")
     * @Apidoc\Returned(ref="app\common\model\cms\SettingModel\InfoReturn")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\SettingModel\editParam")
     * @Apidoc\Param(ref="diyConParam")
     */
    public function edit()
    {
        $param['logo_id']     = $this->param('logo_id/d', 0);
        $param['name']        = $this->param('name/s', '');
        $param['title']       = $this->param('title/s', '');
        $param['keywords']    = $this->param('keywords/s', '');
        $param['description'] = $this->param('description/s', '');
        $param['icp']         = $this->param('icp/s', '');
        $param['copyright']   = $this->param('copyright/s', '');
        $param['off_acc_id']  = $this->param('off_acc_id/d', 0);
        $param['address']     = $this->param('address/s', '');
        $param['tel']         = $this->param('tel/s', '');
        $param['mobile']      = $this->param('mobile/s', '');
        $param['email']       = $this->param('email/s', '');
        $param['qq']          = $this->param('qq/s', '');
        $param['wechat']      = $this->param('wechat/s', '');
        $param['diy_config']  = $this->param('diy_config/a', []);
        $param['is_comment']  = $this->param('is_comment/d', 1);

        validate(SettingValidate::class)->scene('edit')->check($param);

        $param['diy_config'] = serialize($param['diy_config']);
        $data = SettingService::edit($param);

        return success($data);
    }
}
