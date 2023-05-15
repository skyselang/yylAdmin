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
     * @Apidoc\Returned(ref="app\common\service\member\SettingService\info", field="token_type,token_name,diy_con_obj")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function member()
    {
        $data = MemberSetting::info('captcha_register,captcha_login,token_type,token_name,diy_config,diy_con_obj');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置")
     * @Apidoc\Returned(ref="app\common\model\content\SettingModel", withoutField="create_uid,update_uid,create_time,update_time")
     * @Apidoc\Returned(ref="app\common\service\content\SettingService\info")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function content()
    {
        $data = ContentSetting::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("文件设置")
     * @Apidoc\Returned(ref="app\common\model\file\SettingModel", field="is_upload_api,storage,image_ext,image_size,video_ext,video_size,audio_ext,audio_size,word_ext,word_size,other_ext,other_size")
     * @Apidoc\Returned(ref="app\common\service\file\SettingService\info", field="accept_ext")
     */
    public function file()
    {
        $data = FileSetting::info('is_upload_api,storage,image_ext,image_size,video_ext,video_size,audio_ext,audio_size,word_ext,word_size,other_ext,other_size,accept_ext');

        return success($data);
    }

    /**
     * @Apidoc\Title("设置管理")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel")
     * @Apidoc\Returned(ref="app\common\service\setting\SettingService\info")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function setting()
    {
        $data = SettingService::info();

        return success($data);
    }
}
