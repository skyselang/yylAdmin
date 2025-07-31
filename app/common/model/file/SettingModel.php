<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\file;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 文件设置模型
 */
class SettingModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'file_setting';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'setting_id';

    /**
     * 修改前台文件功能文件类型
     * @param mixed $value 数据
     * @return string
     */
    public function setApiFileTypesAttr($value)
    {
        if (empty($value[0] ?? '')) {
            return '';
        }
        return implode(',', $value);
    }
    /**
     * 获取前台文件功能文件类型
     * @param mixed $value 数据
     * @return array
     */
    public function getApiFileTypesAttr($value)
    {
        if (empty($value)) {
            return [];
        }
        return explode(',', $value);
    }

    /**
     * 修改前台文件功能分组ID
     * @param mixed $value 数据
     * @return string
     */
    public function setApiFileGroupIdsAttr($value)
    {
        if (empty($value[0] ?? '')) {
            return '';
        }
        return implode(',', $value);
    }
    /**
     * 获取前台文件功能分组ID
     * @param mixed $value 数据
     * @return array
     */
    public function getApiFileGroupIdsAttr($value)
    {
        if (empty($value)) {
            return [];
        }
        $tag_ids = explode(',', $value);
        foreach ($tag_ids as &$val) {
            $val = (int) $val;
        }
        return $tag_ids;
    }

    /**
     * 修改前台文件功能标签ID
     * @param mixed $value 数据
     * @return string
     */
    public function setApiFileTagIdsAttr($value)
    {
        if (empty($value[0] ?? '')) {
            return '';
        }
        return implode(',', $value);
    }
    /**
     * 获取前台文件功能标签ID
     * @param mixed $value 数据
     * @return array
     */
    public function getApiFileTagIdsAttr($value)
    {
        if (empty($value)) {
            return [];
        }
        $group_ids = explode(',', $value);
        foreach ($group_ids as &$val) {
            $val = (int) $val;
        }
        return $group_ids;
    }
}
