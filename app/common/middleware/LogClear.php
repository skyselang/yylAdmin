<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\member\LogCache;
use app\common\cache\system\UserLogCache;
use app\common\service\member\SettingService as MemberSetting;
use app\common\service\member\LogService;
use app\common\service\system\SettingService as SystemSetting;
use app\common\service\system\UserLogService;

/**
 * 日志清除中间件
 */
class LogClear
{
    /**
     * 处理请求
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 会员日志清除
        $setting = MemberSetting::info();
        if ($setting['log_save_time']) {
            $member_clear_key = 'clear';
            $member_clear_val = LogCache::get($member_clear_key);
            if (empty($member_clear_val)) {
                $member_days = $setting['log_save_time'];
                $member_date = date('Y-m-d H:i:s', strtotime("-{$member_days} day"));
                $member_where[] = ['create_time', '<', $member_date];
                LogCache::set($member_clear_key, $member_days, 7200);
                LogService::clear($member_where);
            }
        }

        // 用户日志清除
        $system = SystemSetting::info();
        if ($system['log_save_time']) {
            $user_clear_key = 'clear';
            $user_clear_val = UserLogCache::get($user_clear_key);
            if (empty($user_clear_val)) {
                $user_days = $system['log_save_time'];
                $user_date = date('Y-m-d H:i:s', strtotime("-{$user_days} day"));
                $user_where[] = ['create_time', '<', $user_date];
                UserLogCache::set($user_clear_key, $user_days, 7200);
                UserLogService::clear($user_where);
            }
        }

        return $next($request);
    }
}
