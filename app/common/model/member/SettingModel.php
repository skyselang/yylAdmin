<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员设置模型
 */
class SettingModel extends Model
{
    // 表名
    protected $name = 'member_setting';
    // 表主键
    protected $pk = 'setting_id';

    // 修改自定义设置
    public function setDiyConfigAttr($value)
    {
        return serialize($value);
    }
    // 获取自定义设置
    public function getDiyConfigAttr($value)
    {
        return unserialize($value);
    }
    // 获取自定义设置对象
    public function getDiyConObjAttr($value, $data)
    {
        $diy_config = is_array($data['diy_config']) ? $data['diy_config'] : unserialize($data['diy_config']);
        foreach ($diy_config as $diy) {
            $diy_con_obj[$diy['config_key']] = $diy['config_val'];
        }
        return $diy_con_obj ?? [];
    }
}
