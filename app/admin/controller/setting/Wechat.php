<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\BaseController;
use app\common\validate\setting\WechatValidate;
use app\common\service\setting\WechatService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信设置")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("535")
 */
class Wechat extends BaseController
{
    /**
     * @Apidoc\Title("公众号信息")
     * @Apidoc\Returned(ref="app\common\model\setting\WechatModel\offiInfoParam")
     * @Apidoc\Returned(ref="app\common\model\setting\WechatModel\qrcode_url")
     */
    public function offiInfo()
    {
        $data = WechatService::offiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("公众号修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\WechatModel\offiInfoParam")
     */
    public function offiEdit()
    {
        $param['name']              = $this->param('name/s', '');
        $param['origin_id']         = $this->param('origin_id/s', '');
        $param['qrcode_id']         = $this->param('qrcode_id/d', 0);
        $param['appid']             = $this->param('appid/s', '');
        $param['appsecret']         = $this->param('appsecret/s', '');
        $param['token']             = $this->param('token/s', '');
        $param['encoding_aes_key']  = $this->param('encoding_aes_key/s', '');
        $param['encoding_aes_type'] = $this->param('encoding_aes_type/d', 1);

        validate(WechatValidate::class)->scene('offiEdit')->check($param);

        $data = WechatService::offiEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序信息")
     * @Apidoc\Returned(ref="app\common\model\setting\WechatModel\miniInfoParam")
     * @Apidoc\Returned(ref="app\common\model\setting\WechatModel\qrcode_url")
     */
    public function miniInfo()
    {
        $data = WechatService::miniInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\WechatModel\miniInfoParam")
     */
    public function miniEdit()
    {
        $param['name']      = $this->param('name/s', '');
        $param['origin_id'] = $this->param('origin_id/s', '');
        $param['qrcode_id'] = $this->param('qrcode_id/d', 0);
        $param['appid']     = $this->param('appid/s', '');
        $param['appsecret'] = $this->param('appsecret/s', '');

        validate(WechatValidate::class)->scene('miniEdit')->check($param);

        $data = WechatService::miniEdit($param);

        return success($data);
    }
}
