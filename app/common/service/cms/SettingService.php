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

class SettingService
{
    // 内容设置表名
    protected static $db_name = 'cms_setting';
    // 内容设置id
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
            $setting = Db::name(self::$db_name)
                ->where('setting_id', $setting_id)
                ->find();

            if (empty($setting)) {
                $setting['setting_id']  = $setting_id;
                $setting['create_time'] = datetime();
                Db::name(self::$db_name)
                    ->insert($setting);

                $setting = Db::name(self::$db_name)
                    ->where('setting_id', $setting_id)
                    ->find();
            }

            $setting['logo_url']    = file_url($setting['logo']);
            $setting['off_acc_url'] = file_url($setting['off_acc']);

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

        $res = Db::name(self::$db_name)
            ->where('setting_id', $setting_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

        return $param;
    }
}
