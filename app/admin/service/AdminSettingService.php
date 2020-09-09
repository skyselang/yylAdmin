<?php
/*
 * @Description  : 系统设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-04
 * @LastEditTime : 2020-09-09
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Cache;
use app\cache\AdminSettingCache;

class AdminSettingService
{
    // 默认设置id
    static $admin_setting_id = 1;

    /**
     * 缓存设置
     *
     * @return bool
     */
    public static function settingCache()
    {
        $res = Cache::clear(); //缓存清除

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
        $admin_setting_id = self::$admin_setting_id;

        if ($param) {
            $admin_verify['switch'] = $param['switch'];
            $admin_verify['curve']  = $param['curve'];
            $admin_verify['noise']  = $param['noise'];
            $admin_verify['bgimg']  = $param['bgimg'];
            $admin_verify['type']   = $param['type'];
            $admin_verify['length'] = $param['length'];
            $admin_verify['expire'] = $param['expire'];

            $update['admin_verify'] = serialize($admin_verify);
            $update['update_time']  = date('Y-m-d H:i:s');
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($update);

            if (empty($admin_setting)) {
                error();
            } else {
                AdminSettingCache::del($admin_setting_id);
            }
        } else {
            $admin_setting = self::admin_setting();
            $admin_verify  = $admin_setting['admin_verify'];
        }

        return $admin_verify;
    }

    /**
     * Token设置
     *
     * @param array $param token参数
     *
     * @return array
     */
    public static function settingToken($param = [])
    {
        $admin_setting_id = self::$admin_setting_id;

        if ($param) {
            $admin_token['iss'] = $param['iss'];
            $admin_token['exp'] = $param['exp'];

            $update['admin_token'] = serialize($admin_token);
            $update['update_time'] = date('Y-m-d H:i:s');
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->update($update);

            if (empty($admin_setting)) {
                error();
            } else {
                AdminSettingCache::del($admin_setting_id);
            }
        } else {
            $admin_setting = self::admin_setting();
            $admin_token   = $admin_setting['admin_token'];
        }

        return $admin_token;
    }

    /**
     * 默认设置
     *
     * @return array
     */
    public static function admin_setting()
    {
        $admin_setting_id = self::$admin_setting_id;

        $admin_setting = AdminSettingCache::get($admin_setting_id);

        if (empty($admin_setting)) {
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->find();

            if (empty($admin_setting)) {
                $admin_setting['admin_setting_id'] = $admin_setting_id;
                $admin_setting['admin_verify']     = serialize([]);
                $admin_setting['admin_token']      = serialize([]);
                $admin_setting['create_time']      = date('Y-m-d H:i:s');
                Db::name('admin_setting')->insert($admin_setting);
            }

            // 验证码
            $admin_verify = unserialize($admin_setting['admin_verify']);
            if (empty($admin_verify)) {
                $admin_verify['switch'] = false;  //开关
                $admin_verify['curve']  = false;  //曲线
                $admin_verify['noise']  = false;  //杂点 
                $admin_verify['bgimg']  = false;  //背景图
                $admin_verify['type']   = 1;      //类型：1数字，2字母，3数字字母，4算术，5中文
                $admin_verify['length'] = 4;      //位数3-6位
                $admin_verify['expire'] = 180;    //有效时间（秒）
            }

            // Token
            $admin_token = unserialize($admin_setting['admin_token']);
            if (empty($admin_token)) {
                $admin_token['iss'] = 'yylAdmin';  //签发者
                $admin_token['exp'] = 0.5;         //有效时间（天）
            }

            $admin_setting['admin_verify'] = serialize($admin_verify);
            $admin_setting['admin_token']  = serialize($admin_token);
            $admin_setting['update_time']  = date('Y-m-d H:i:s');
            Db::name('admin_setting')->where('admin_setting_id', $admin_setting_id)->update($admin_setting);

            AdminSettingCache::set($admin_setting_id, $admin_setting);

            $admin_setting['admin_verify'] = $admin_verify;
            $admin_setting['admin_token']  = $admin_token;
        } else {
            $admin_setting['admin_verify'] = unserialize($admin_setting['admin_verify']);
            $admin_setting['admin_token']  = unserialize($admin_setting['admin_token']);
        }

        return $admin_setting;
    }
}
