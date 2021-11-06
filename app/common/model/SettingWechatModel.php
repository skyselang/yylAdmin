<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 微信设置模型
namespace app\common\model;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class SettingWechatModel extends Model
{
    // 表名
    protected $name = 'setting_wechat';
    // 表主键
    protected $pk = 'setting_wechat_id';

    /**
     * @Apidoc\Field("name,origin_id,qrcode,appid,appsecret,url,token,encoding_aes_key,encoding_aes_type")
     */
    public function offiInfoParam()
    {
    }

    /**
     * @Apidoc\Field("name,origin_id,qrcode,appid,appsecret")
     */
    public function miniInfoParam()
    {
    }

    /**
     * @Apidoc\Field("qrcode_url")
     * @Apidoc\AddField("qrcode_url",type="string",default=" ",desc="二维码链接")
     */
    public function qrcode_url()
    {
    }
}
