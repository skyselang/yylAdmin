<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use app\common\controller\BaseController;
use app\common\service\setting\WechatService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("410")
 */
class Wechat extends BaseController
{
    /**
     * @Apidoc\Title("微信公众号接入")
     */
    public function access()
    {
        $app = WechatService::offi();

        $app->server->push(function ($message) {
            return '您好！欢迎使用 yylAdmin !';
        });

        $response = $app->server->serve();

        $response->send();

        exit;
    }
}
