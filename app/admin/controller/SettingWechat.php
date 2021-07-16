<?php
/*
 * @Description  : 微信设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-07-16
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\SettingWechatValidate;
use app\common\service\SettingWechatService;
use app\common\service\UploadService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信设置")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("90")
 */
class SettingWechat
{
    /**
     * @Apidoc\Title("公众号信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\SettingWechatModel\offiInfo")
     * )
     */
    public function offiInfo()
    {
        $data = SettingWechatService::offiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("公众号修改")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingWechatModel\offiEdit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function offiEdit()
    {
        $param['name']              = Request::param('name/s', '');
        $param['origin_id']         = Request::param('origin_id/s', '');
        $param['qrcode']            = Request::param('qrcode/s', '');
        $param['appid']             = Request::param('appid/s', '');
        $param['appsecret']         = Request::param('appsecret/s', '');
        $param['token']             = Request::param('token/s', '');
        $param['encoding_aes_key']  = Request::param('encoding_aes_key/s', '');
        $param['encoding_aes_type'] = Request::param('encoding_aes_type/d', 1);

        validate(SettingWechatValidate::class)->scene('offiEdit')->check($param);

        $data = SettingWechatService::offiEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\SettingWechatModel\miniInfo")
     * )
     */
    public function miniInfo()
    {
        $data = SettingWechatService::miniInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("小程序修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingWechatModel\miniEdit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function miniEdit()
    {
        $param['name']      = Request::param('name/s', '');
        $param['origin_id'] = Request::param('origin_id/s', '');
        $param['qrcode']    = Request::param('qrcode/s', '');
        $param['appid']     = Request::param('appid/s', '');
        $param['appsecret'] = Request::param('appsecret/s', '');

        validate(SettingWechatValidate::class)->scene('miniEdit')->check($param);

        $data = SettingWechatService::miniEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("上传二维码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="paramFile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnFile")
     */
    public function qrcode()
    {
        $param['qrcode'] = Request::file('file');

        validate(SettingWechatValidate::class)->scene('qrcode')->check($param);

        $data = UploadService::upload($param['qrcode'], 'setting/wechat');

        return success($data);
    }
}
