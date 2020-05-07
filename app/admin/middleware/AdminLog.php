<?php
/*
 * @Description  : 日志
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-05-06
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use think\facade\Db;

class AdminLog
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
        $response = $next($request);

        $is_admin_log = Config::get('admin.is_admin_log', false);
        if ($is_admin_log) {
            $data['admin_user_id'] = $request->header('Admin-User-Id', 0);
            $data['menu_url'] = app('http')->getName() . '/' . $request->pathinfo();
            $data['request_method'] =  $request->method();
            $data['request_ip'] = $request->ip();
            $data['request_param'] = serialize($request->param());
            $data['insert_time'] = date('Y-m-d H:i:s');
            Db::name('admin_log')->insert($data);
        }

        return $response;
    }
}
