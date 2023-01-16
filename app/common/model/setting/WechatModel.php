<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\setting;

use think\Model;
use app\common\model\file\FileModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 微信设置模型
 */
class WechatModel extends Model
{
    // 表名
    protected $name = 'setting_wechat';
    // 表主键
    protected $pk = 'setting_id';

    // 关联qrcode文件
    public function qrcode()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'qrcode_id')->append(['file_url'])->where(where_disdel());
    }
    // 获取qrcode链接
    public function getQrcodeUrlAttr($value, $data)
    {
        return $this['qrcode']['file_url'] ?? '';
    }
}
