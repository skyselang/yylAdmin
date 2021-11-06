<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容设置
namespace app\common\service\cms;

use think\facade\Db;
use app\common\cache\cms\SettingCache;
use app\common\service\file\FileService;

class SettingService
{
    // 表名
    protected static $t_name = 'cms_setting';
    // 表主键
    protected static $t_pk = 'setting_id';
    // 设置id
    protected static $setting_id = 1;

    /**
     * 内容设置信息
     *
     * @return array
     */
    public static function info()
    {
        $setting_id = self::$setting_id;

        $setting = SettingCache::get($setting_id);
        if (empty($setting)) {
            $setting = Db::name(self::$t_name)
                ->where(self::$t_pk, $setting_id)
                ->find();
            if (empty($setting)) {
                $setting[self::$t_pk]  = $setting_id;
                $setting['create_time'] = datetime();
                Db::name(self::$t_name)
                    ->insert($setting);

                $setting = Db::name(self::$t_name)
                    ->where(self::$t_pk, $setting_id)
                    ->find();
            }

            $setting['logo_url']    = FileService::fileUrl($setting['logo_id']);
            $setting['off_acc_url'] = FileService::fileUrl($setting['off_acc_id']);

            SettingCache::set($setting_id, $setting);
        }

        return $setting;
    }

    /**
     * 内容设置修改
     *
     * @param array $param 内容信息
     *
     * @return array
     */
    public static function edit($param)
    {
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

        return $param;
    }
}
