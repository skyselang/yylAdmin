<?php
/*
 * @Description  : 内容设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-03
 */

namespace app\admin\controller;

use think\facade\Request;
use hg\apidoc\annotation as Apidoc;
use app\common\validate\SettingCmsValidate;
use app\common\service\SettingCmsService;

/**
 * @Apidoc\Title("内容设置")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("999")
 */
class SettingCms
{
    /**
     * @Apidoc\Title("内容设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\SettingCmsModel\Info")
     * )
     */
    public function cmsInfo()
    {
        $data = SettingCmsService::cmsInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingCmsModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function cmsEdit()
    {
        $param['logo']        = Request::param('logo/s', '');
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
        $param['off_acc']     = Request::param('off_acc/s', '');

        validate(SettingCmsValidate::class)->scene('edit')->check($param);

        $data = SettingCmsService::cmsEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置上传")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="file")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned("path", type="string", desc="路径"),
     *      @Apidoc\Returned("url", type="string", desc="链接"),
     * )
     */
    public function cmsUpload()
    {
        $param['file']  = Request::file('file');
        $param['image'] = $param['file'];

        validate(SettingCmsValidate::class)->scene('image')->check($param);

        $data = SettingCmsService::cmsUpload($param);

        return success($data);
    }
}
