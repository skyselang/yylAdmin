<?php
/*
 * @Description  :微信配置模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-04-23
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class WechatConfigModel extends Model
{
    protected $name = 'wechat_config';

    /**
     * @Field("name,origin_id,qrcode,appid,appsecret,url,token,encoding_aes_key,encoding_aes_type")
     * @AddField("qrcode_url",type="string",default="",desc="二维码链接")
     */
    public function offiInfo()
    {
    }

    /**
     * @Field("name,origin_id,qrcode,appid,appsecret,url,token,encoding_aes_key,encoding_aes_type")
     */
    public function offiEdit()
    {
    }

    /**
     * @Field("name,origin_id,qrcode,appid,appsecret")
     * @AddField("qrcode_url",type="string",default="",desc="二维码链接")
     */
    public function miniInfo()
    {
    }

    /**
     * @Field("name,origin_id,qrcode,appid,appsecret")
     */
    public function miniEdit()
    {
    }
}
