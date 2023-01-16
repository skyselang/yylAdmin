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
use app\common\service\setting\WechatService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信设置")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("600")
 */
class Wechat extends BaseController
{
    /**
     * @Apidoc\Title("公众号信息")
     * @Apidoc\Returned(ref="app\common\model\setting\WechatModel", field="name,origin_id,qrcode_id,appid,appsecret,token,encoding_aes_key,encoding_aes_type",
     *   @Apidoc\Returned("qrcode_url", type="string", desc="二维码链接"),
     *   @Apidoc\Returned("server_url", type="string", desc="服务器地址"),
     * )
     */
    public function offiInfo()
    {
        $data = WechatService::offiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("公众号修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\WechatModel", field="name,origin_id,qrcode_id,appid,appsecret,token,encoding_aes_key,encoding_aes_type")
     */
    public function offiEdit()
    {
        $param['name']              = $this->request->param('name/s', '');
        $param['origin_id']         = $this->request->param('origin_id/s', '');
        $param['qrcode_id']         = $this->request->param('qrcode_id/d', 0);
        $param['appid']             = $this->request->param('appid/s', '');
        $param['appsecret']         = $this->request->param('appsecret/s', '');
        $param['token']             = $this->request->param('token/s', '');
        $param['encoding_aes_key']  = $this->request->param('encoding_aes_key/s', '');
        $param['encoding_aes_type'] = $this->request->param('encoding_aes_type/d', 1);

        $data = WechatService::offiEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序信息")
     * @Apidoc\Returned(ref="app\common\model\setting\WechatModel", field="name,origin_id,qrcode_id,appid,appsecret",
     *   @Apidoc\Returned("qrcode_url", type="string", desc="小程序码链接"),
     * )
     */
    public function miniInfo()
    {
        $data = WechatService::miniInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\WechatModel", field="name,origin_id,qrcode_id,appid,appsecret")
     */
    public function miniEdit()
    {
        $param['name']      = $this->request->param('name/s', '');
        $param['origin_id'] = $this->request->param('origin_id/s', '');
        $param['qrcode_id'] = $this->request->param('qrcode_id/d', 0);
        $param['appid']     = $this->request->param('appid/s', '');
        $param['appsecret'] = $this->request->param('appsecret/s', '');

        $data = WechatService::miniEdit($param);

        return success($data);
    }
}
