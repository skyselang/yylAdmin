<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\system;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 短信日志模型
 */
class SmsLogModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_sms_log';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'log_id';

    /**
     * 修改模板变量
     * @param mixed $value 数据
     * @return string
     */
    public function setDataAttr($value)
    {
        return json_encode($value);
    }
    /**
     * 获取模板变量
     * @param mixed $value 数据
     * @return array
     */
    public function getDataAttr($value)
    {
        return json_decode($value, true);
    }
}
