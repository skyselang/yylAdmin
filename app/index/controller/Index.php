<?php
/*
 * @Description  : 首页
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-17
 * @LastEditTime : 2021-05-25
 */

namespace app\index\controller;

use app\index\service\IndexService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("首页")
 * @Apidoc\Sort("1")
 */
class Index
{
    /**
     * @Apidoc\Title("首页")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = '后端安装成功，感谢使用，如有帮助，欢迎Star！';

        return success($data, $msg);
    }
}
