<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 日志清除中间件
namespace app\common\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\MemberLogCache;
use app\common\cache\admin\UserLogCache;
use app\common\service\SettingService;
use app\common\service\MemberLogService;
use app\common\service\admin\SettingService as AdminSettingService;
use app\common\service\admin\UserLogService;

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
        $member_setting = SettingService::logInfo();
        if ($member_setting['log_save_time']) {
            $member_clear_key = 'clear';
            $member_clear_val = MemberLogCache::get($member_clear_key);
            if (empty($member_clear_val)) {
                $member_days = $member_setting['log_save_time'];
                $member_date = date('Y-m-d H:i:s', strtotime("-{$member_days} day"));
                $mmeber_where[] = ['create_time', '<=', $member_date];
                MemberLogService::clear($mmeber_where);
                MemberLogCache::set($member_clear_key, $member_days, 86400);
            }
        }

        // 用户日志清除
        $user_seting = AdminSettingService::logInfo();
        if ($user_seting['log_save_time']) {
            $user_clear_key = 'clear';
            $user_clear_val = UserLogCache::get($user_clear_key);
            if (empty($user_clear_val)) {
                $user_days = $user_seting['log_save_time'];
                $user_date = date('Y-m-d H:i:s', strtotime("-{$user_days} day"));
                $user_where[] = ['create_time', '<=', $user_date];
                UserLogService::clear($user_where);
                UserLogCache::set($user_clear_key, $user_days, 86400);
            }
        }

        return $next($request);
    }
}