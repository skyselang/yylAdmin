<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\setting\SettingValidate as Validate;
use app\common\service\setting\SettingService as Service;

/**
 * @Apidoc\Title("lang(设置管理)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("100")
 */
class Setting extends BaseController
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
     * @Apidoc\Title("lang(基本设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="favicon_id,logo_id,name,title,keywords,description,icp,copyright,favicon_url,logo_url")
     */
    public function basicInfo()
    {
        $data = $this->service::info('favicon_id,logo_id,name,title,keywords,description,icp,copyright,favicon_url,logo_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(基本设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="favicon_id,logo_id,name,title,keywords,description,icp,copyright")
     */
    public function basicEdit()
    {
        $param = $this->params([
            'favicon_id/d'  => 0,
            'logo_id/d'     => 0,
            'name/s'        => '',
            'title/s'       => '',
            'keywords/s'    => '',
            'description/s' => '',
            'icp/s'         => '',
            'copyright/s'   => '',
        ]);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(联系设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="offi_id,mini_id,video_id,douyin_id,address,tel,fax,mobile,email,qq,wechat,offi_url,mini_url,video_url,douyin_url")
     */
    public function contactInfo()
    {
        $data = $this->service::info('offi_id,mini_id,douyin_id,video_id,address,tel,fax,mobile,email,qq,wechat,offi_url,mini_url,video_url,douyin_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(联系设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="offi_id,mini_id,video_id,douyin_id,address,tel,fax,mobile,email,qq,wechat")
     */
    public function contactEdit()
    {
        $param = $this->params([
            'offi_id/d'   => 0,
            'mini_id/d'   => 0,
            'video_id/d'  => 0,
            'douyin_id/d' => 0,
            'address/s'   => '',
            'tel/s'       => '',
            'fax/s'       => '',
            'mobile/s'    => '',
            'email/s'     => '',
            'qq/s'        => '',
            'wechat/s'    => '',
        ]);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }
}
