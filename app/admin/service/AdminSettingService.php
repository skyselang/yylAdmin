<?php
/*
 * @Description  : 系统设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-04
 * @LastEditTime : 2020-09-04
 */

namespace app\admin\service;

use app\cache\AdminVerifyCache;
use think\facade\Cache;
use think\facade\Db;

class AdminSettingService
{
    /**
     * 缓存设置
     *
     * @return bool
     */
    public static function settingCache()
    {
        $res = Cache::clear();

        return $res;
    }

    /**
     * 验证码设置
     *
     * @param array $param 验证码参数
     *
     * @return array
     */
    public static function settingVerify($param = [])
    {
        $admin_setting_id = 1;

        if ($param) {
            $admin_verify['verify_switch'] = $param['verify_switch'];
            $admin_verify['verify_curve']  = $param['verify_curve'];
            $admin_verify['verify_noise']  = $param['verify_noise'];
            $admin_verify['verify_bgimg']  = $param['verify_bgimg'];
            $admin_verify['verify_type']   = $param['verify_type'];
            $admin_verify['verify_length'] = $param['verify_length'];
            $admin_verify['verify_expire'] = $param['verify_expire'];

            $update['admin_verify'] = serialize($admin_verify);
            $update['update_time']  = date('Y-m-d H:i:s');
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($update);
            if (empty($admin_setting)) {
                error();
            }

            AdminVerifyCache::del($admin_setting_id);
        } else {
            $admin_setting = Db::name('admin_setting')
                ->field('admin_setting_id,admin_verify')
                ->where('admin_setting_id', $admin_setting_id)
                ->find();
            if (empty($admin_setting)) {
                $admin_verify['verify_switch'] = false;
                $admin_verify['verify_curve']  = false;
                $admin_verify['verify_noise']  = false;
                $admin_verify['verify_bgimg']  = false;
                $admin_verify['verify_type']   = 1;
                $admin_verify['verify_length'] = 4;
                $admin_verify['verify_expire'] = 180;

                $admin_setting['admin_setting_id'] = $admin_setting_id;
                $admin_setting['admin_verify']     = serialize($admin_verify);
                $admin_setting['create_time']      = date('Y-m-d H:i:s');
                Db::name('admin_setting')->insert($admin_setting);
            } else {
                $admin_verify = unserialize($admin_setting['admin_verify']);
            }
        }

        return $admin_verify;
    }
}
