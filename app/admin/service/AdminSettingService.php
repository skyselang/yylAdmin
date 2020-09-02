<?php
/*
 * @Description  : 设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-04
 * @LastEditTime : 2020-09-02
 */

namespace app\admin\service;

use think\facade\Cache;

class AdminSettingService
{
    /**
     * 清除缓存
     *
     * @return bool
     */
    public static function settingCache()
    {
        $res = Cache::clear();

        return $res;
    }
}
