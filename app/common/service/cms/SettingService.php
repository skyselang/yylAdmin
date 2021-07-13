<?php
/*
 * @Description  : 内容设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-13
 */

namespace app\common\service\cms;

use think\facade\Db;
use think\facade\Filesystem;
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

    /**
     * 内容设置上传
     *
     * @param array $param 文件
     * 
     * @return array
     */
    public static function upload($param)
    {
        $file = $param['file'];

        $name = Filesystem::disk('public')
            ->putFile('cms/setting', $file, function () use ($file) {
                return date('Ymd') . '/' . date('YmdHis');
            });

        $data['path'] = 'storage/' . $name;
        $data['url']  = file_url($data['path']);

        return $data;
    }
}
