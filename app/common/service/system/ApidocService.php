<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use hg\apidoc\annotation as Apidocs;

/**
 * 接口文档
 */
class ApidocService
{
    /**
     * 接口文档
     * @return array
     * @Apidocs\Returned("apidoc_url", type="string", desc="接口文档链接")
     * @Apidocs\Returned("apidoc_pwd", type="string", desc="接口文档密码")
     */
    public static function apidoc()
    {
        $data['apidoc_url'] = server_url() . '/' . config('apidoc.dir_name');
        $data['apidoc_pwd'] = config('apidoc.auth.password');

        return $data;
    }
}
