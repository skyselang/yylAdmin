<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\service\member\SettingService as MemberSetting;
use app\common\service\content\SettingService as ContentSetting;
use app\common\service\file\SettingService as FileSetting;
use app\common\service\setting\SettingService;
use app\common\service\setting\LinkService;
use app\common\service\setting\NoticeService;

/**
 * @Apidoc\Title("lang(设置)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("100")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("lang(设置信息)")
     * @Apidoc\Returned("member", type="object", desc="会员设置", children={
     *   @Apidoc\Returned(ref={MemberSetting::class,"info"}, field="is_captcha_register,is_captcha_login,is_register,is_login,is_captcha_register,is_captcha_login,is_phone_register,is_phone_login,is_email_register,is_email_login,default_avatar_url,token_type,token_name"),
     * })
     * @Apidoc\Returned("content", type="object", desc="内容设置", children={
     *   @Apidoc\Returned(ref={ContentSetting::class,"info"}, withoutField="create_uid,update_uid,create_time,update_time"),
     * })
     * @Apidoc\Returned("file", type="object", desc="文件设置", children={
     *   @Apidoc\Returned(ref={FileSetting::class,"info"}, field="is_upload_api,storage,image_ext,image_size,video_ext,video_size,audio_ext,audio_size,word_ext,word_size,other_ext,other_size,is_api_file,api_file_types,api_file_group_ids,api_file_tag_ids"),
     * })
     * @Apidoc\Returned("setting", type="object", desc="设置管理", children={
     *   @Apidoc\Returned(ref={SettingService::class,"info"}, withoutField="create_uid,update_uid,create_time,update_time"),
     * })
     * @Apidoc\Returned("link", type="array", desc="友链列表", children={
     *   @Apidoc\Returned(ref={LinkService::class,"info"}, field="link_id,image_id,name,name_color,url,desc,image_url"),
     * })
     * @Apidoc\Returned("notice", type="array", desc="通告列表", children={
     *   @Apidoc\Returned(ref={NoticeService::class,"info"}, field="notice_id,unique,image_id,type,title,title_color,start_time,end_time,image_url"),
     * })
     */
    public function setting()
    {
        // 会员设置
        $data['member'] = MemberSetting::info('is_captcha_register,is_captcha_login,is_register,is_login,is_captcha_register,is_captcha_login,is_phone_register,is_phone_login,is_email_register,is_email_login,default_avatar_url,token_type,token_name');
        // 内容设置
        $data['content'] = ContentSetting::info();
        // 文件设置
        $data['file'] = FileSetting::info('is_upload_api,storage,image_ext,image_size,video_ext,video_size,audio_ext,audio_size,word_ext,word_size,other_ext,other_size,accept_ext,is_api_file,api_file_types,api_file_group_ids,api_file_tag_ids');
        // 设置管理
        $data['setting'] = SettingService::info('', 'email_host,email_secure,email_port,email_username,email_password,email_setfrom');
        // 友链列表
        $link_field = 'unique,image_id,name,name_color,url,desc';
        $link_where = where_disdel([['start_time', '<=', datetime()], ['end_time', '>=', datetime()]]);
        $link_order = ['sort' => 'desc', 'link_id' => 'desc'];
        $data['link'] = LinkService::list($link_where, 0, 0, $link_order, $link_field, false)['list'] ?? [];
        // 通告列表
        $notice_field = 'unique,image_id,type,title,title_color,start_time,end_time';
        $notice_where = where_disdel([['start_time', '<=', datetime()], ['end_time', '>=', datetime()]]);
        $notice_order = ['sort' => 'desc', 'notice_id' => 'desc'];
        $data['notice'] = NoticeService::list($notice_where, 0, 0, $notice_order, $notice_field, false)['list'] ?? [];

        return success($data);
    }
}
