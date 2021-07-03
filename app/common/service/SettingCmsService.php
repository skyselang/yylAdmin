<?php
/*
 * @Description  : 内容设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-03
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\SettingCmsCache;

class SettingCmsService
{
    // 内容设置id
    private static $setting_cms_id = 1;

    /**
     * 内容设置信息
     *
     * @return array
     */
    public static function cmsInfo()
    {
        $setting_cms_id = self::$setting_cms_id;

        $setting_cms = SettingCmsCache::get($setting_cms_id);
        if (empty($setting_cms)) {
            $setting_cms = Db::name('setting_cms')
                ->where('setting_cms_id', $setting_cms_id)
                ->find();

            if (empty($setting_cms)) {
                $setting_cms['setting_cms_id'] = $setting_cms_id;
                $setting_cms['create_time']    = datetime();
                Db::name('setting_cms')
                    ->insert($setting_cms);

                $setting_cms = Db::name('setting_cms')
                    ->where('setting_cms_id', $setting_cms_id)
                    ->find();
            }

            $setting_cms['logo_url']    = file_url($setting_cms['logo']);
            $setting_cms['off_acc_url'] = file_url($setting_cms['off_acc']);

            SettingCmsCache::set($setting_cms_id, $setting_cms);
        }

        return $setting_cms;
    }

    /**
     * 内容设置修改
     *
     * @param array $param 内容信息
     *
     * @return array
     */
    public static function cmsEdit($param)
    {
        $setting_cms_id = self::$setting_cms_id;

        $param['update_time'] = datetime();

        $res = Db::name('setting_cms')
            ->where('setting_cms_id', $setting_cms_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        SettingCmsCache::del($setting_cms_id);

        return $param;
    }

    /**
     * 内容设置上传
     *
     * @param array $param 文件
     * 
     * @return array
     */
    public static function cmsUpload($param)
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
