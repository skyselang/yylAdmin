<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件设置
namespace app\common\service\file;

use think\facade\Db;
use app\common\cache\file\SettingCache;

class SettingService
{
    // 设置id
    private static $setting_id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $setting_id = self::$setting_id;

        $file_setting = SettingCache::get($setting_id);
        if (empty($file_setting)) {
            $file_setting = Db::name('file_setting')
                ->where('setting_id', $setting_id)
                ->find();
            if (empty($file_setting)) {
                $file_setting['setting_id']  = $setting_id;
                $file_setting['storage']     = 'local';
                $file_setting['create_time'] = datetime();

                Db::name('file_setting')
                    ->insert($file_setting);

                $file_setting = Db::name('file_setting')
                    ->where('setting_id', $setting_id)
                    ->find();
            }

            SettingCache::set($setting_id, $file_setting);
        }

        return $file_setting;
    }

    /**
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @return array
     */
    public static function edit($param)
    {
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $file_setting = Db::name('file_setting')
            ->where('setting_id', $setting_id)
            ->update($param);
        if (empty($file_setting)) {
            exception();
        }

        SettingCache::del($setting_id);

        return $param;
    }
}
