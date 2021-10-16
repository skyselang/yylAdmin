<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容设置控制器
namespace app\admin\controller\cms;

use think\facade\Request;
use app\common\validate\cms\SettingValidate;
use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容设置")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class Setting
{
    /**
     * @Apidoc\Title("内容设置信息")
     * @Apidoc\Returned(ref="app\common\model\cms\SettingModel\Info")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\SettingModel\edit")
     */
    public function edit()
    {
        $param['logo_id']     = Request::param('logo_id/d', 0);
        $param['name']        = Request::param('name/s', '');
        $param['title']       = Request::param('title/s', '');
        $param['keywords']    = Request::param('keywords/s', '');
        $param['description'] = Request::param('description/s', '');
        $param['icp']         = Request::param('icp/s', '');
        $param['copyright']   = Request::param('copyright/s', '');
        $param['address']     = Request::param('address/s', '');
        $param['tel']         = Request::param('tel/s', '');
        $param['mobile']      = Request::param('mobile/s', '');
        $param['email']       = Request::param('email/s', '');
        $param['qq']          = Request::param('qq/s', '');
        $param['wechat']      = Request::param('wechat/s', '');
        $param['off_acc_id']  = Request::param('off_acc_id/d', 0);

        validate(SettingValidate::class)->scene('edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
