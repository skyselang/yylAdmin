<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use app\common\controller\BaseController;
use app\common\service\member\SettingService as MemberSetting;
use app\common\service\content\SettingService as ContentSetting;
use app\common\service\file\SettingService as FileSetting;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("100")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("会员设置")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="captcha_register,captcha_login")
     * @Apidoc\Returned("token_type", type="string", desc="token方式：header、param")
     * @Apidoc\Returned("token_name", type="string", desc="token名称，前后端必须一致")
     * @Apidoc\Returned(ref="diyConReturn")
     * @Apidoc\Returned(ref="diyConObjReturn")
     */
    public function member()
    {
        $setting = MemberSetting::info();

        $data['captcha_register'] = $setting['captcha_register'];
        $data['captcha_login']    = $setting['captcha_login'];
        $data['token_type']       = $setting['token_type'];
        $data['token_name']       = $setting['token_name'];
        $data['diy_config']       = $setting['diy_config'];
        $data['diy_con_obj']      = $setting['diy_con_obj'];

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置")
     * @Apidoc\Returned(ref="app\common\model\content\SettingModel", withoutField="diy_config,create_uid,update_uid,create_time,update_time")
     * @Apidoc\Returned(ref="app\common\service\content\SettingService\info")
     * @Apidoc\Returned(ref="diyConReturn")
     * @Apidoc\Returned(ref="diyConObjReturn")
     */
    public function content()
    {
        $data = ContentSetting::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件设置")
     * @Apidoc\Returned(ref="app\common\model\file\SettingModel", field="is_upload,storage,accept_ext,image_ext,image_size,video_ext,video_size,audio_ext,audio_size,word_ext,word_size,other_ext,other_size")
     * @Apidoc\Returned("accept_ext", type="string", desc="允许上传文件后缀")
     */
    public function file()
    {
        $data = FileSetting::info('is_upload,storage,accept_ext,image_ext,image_size,video_ext,video_size,audio_ext,audio_size,word_ext,word_size,other_ext,other_size');

        return success($data);
    }

    /**
     * @Apidoc\Title("设置管理")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel", withoutField="diy_config")
     * @Apidoc\Returned("feedback_type", type="array", desc="反馈类型")
     * @Apidoc\Returned(ref="diyConReturn")
     * @Apidoc\Returned(ref="diyConObjReturn")
     */
    public function setting()
    {
        $data = SettingService::info();

        return success($data);
    }
}
