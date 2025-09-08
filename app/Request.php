<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app;

/**
 * 应用请求对象类
 */
class Request extends \think\Request
{
    // 全局过滤规则
    protected $filter = [];

    public function __construct()
    {
        parent::__construct();

        // 代理服务器IP
        $proxyServerIp = config('app.proxy_server_ip');
        if (!empty($proxyServerIp)) {
            $this->proxyServerIp = explode(',', $proxyServerIp);
        }
    }
}
