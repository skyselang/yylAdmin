<?php
/*
 * @Description  : 内容设置控制器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-13
 */

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
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\cms\SettingModel\Info")
     * )
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\cms\SettingModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function edit()
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

        validate(SettingValidate::class)->scene('edit')->check($param);

        $data = SettingService::edit($param);

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
    public function upload()
    {
        $param['file']  = Request::file('file');
        $param['image'] = $param['file'];

        validate(SettingValidate::class)->scene('image')->check($param);

        $data = SettingService::upload($param);

        return success($data);
    }
}
