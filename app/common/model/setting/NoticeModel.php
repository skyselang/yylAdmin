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
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * 通告管理模型
 */
class NoticeModel extends Model
{
    // 表名
    protected $name = 'setting_notice';
    // 表主键
    protected $pk = 'notice_id';

    /**
     * 获取类型名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("type_name", type="string", desc="类型名称")
     */
    public function getTypeNameAttr($value, $data)
    {
        return SettingService::notice_types($data['type']);
    }
}
