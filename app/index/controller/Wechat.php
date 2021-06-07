<?php
/*
 * @Description  : 微信
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-04
 * @LastEditTime : 2021-06-05
 */

namespace app\index\controller;

use app\common\service\WechatService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("微信")
 * @Apidoc\Sort("9")
 */
class Wechat
{
    /**
     * @Apidoc\Title("微信公众号接入")
     */
    public function access()
    {
        $app = WechatService::offi();

        $app->server->push(function ($message) {
            return "您好！感谢使用 yylAdmin !";
        });

        $response = $app->server->serve();
        
        $response->send();

        exit;
    }
}
