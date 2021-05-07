<?php
/*
 * @Description  : 微信设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\WechatSettingValidate;
use app\common\service\WechatSettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信设置")
 * @Apidoc\Group("index")
 */
class WechatSetting
{
    /**
     * @Apidoc\Title("公众号信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\WechatSettingModel\offiInfo")
     * )
     */
    public function offiInfo()
    {
        $data = WechatSettingService::offiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("公众号修改")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\WechatSettingModel\offiEdit")
     * @Apidoc\Returned(ref="return")
     */
    public function offiEdit()
    {
        $param['name']              = Request::param('name/s', '');
        $param['origin_id']         = Request::param('origin_id/s', '');
        $param['qrcode']            = Request::param('qrcode/s', '');
        $param['appid']             = Request::param('appid/s', '');
        $param['appsecret']         = Request::param('appsecret/s', '');
        $param['url']               = Request::param('url/s', '');
        $param['token']             = Request::param('token/s', '');
        $param['encoding_aes_key']  = Request::param('encoding_aes_key/s', '');
        $param['encoding_aes_type'] = Request::param('encoding_aes_type/d', 1);

        validate(WechatSettingValidate::class)->scene('offiEdit')->check($param);

        $data = WechatSettingService::offiEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\WechatSettingModel\miniInfo")
     * )
     */
    public function miniInfo()
    {
        $data = WechatSettingService::miniInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\WechatSettingModel\miniEdit")
     * @Apidoc\Returned(ref="return")
     */
    public function miniEdit()
    {
        $param['name']      = Request::param('name/s', '');
        $param['origin_id'] = Request::param('origin_id/s', '');
        $param['qrcode']    = Request::param('qrcode/s', '');
        $param['appid']     = Request::param('appid/s', '');
        $param['appsecret'] = Request::param('appsecret/s', '');

        validate(WechatSettingValidate::class)->scene('miniEdit')->check($param);

        $data = WechatSettingService::miniEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("上传二维码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("type", type="string", require=true, default="offi", desc="offi公众号、mini小程序")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="二维码图片")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("type", type="string", desc="offi公众号、mini小程序"),
     *      @Apidoc\Returned("file_path", type="string", desc="二维码路径"),
     *      @Apidoc\Returned("file_url", type="string", desc="二维码链接"),
     * )
     */
    public function qrcode()
    {
        $param['type']   = Request::param('type/s', 'offi');
        $param['file']   = Request::file('file');
        $param['qrcode'] = $param['file'];

        validate(WechatSettingValidate::class)->scene('qrcode')->check($param);

        $data = WechatSettingService::qrcode($param);

        return success($data);
    }
}
