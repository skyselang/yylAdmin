<?php
/*
 * @Description  : 设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-04
 * @LastEditTime : 2020-08-04
 */

namespace app\admin\controller;

use app\admin\service\AdminSettingService;

class AdminSetting
{
    /**
     * 清除缓存
     *
     * @method GET
     *
     * @return json
     */
    public function cacheClear()
    {
        $data = AdminSettingService::cacheclear();

        return success($data);
    }
}
