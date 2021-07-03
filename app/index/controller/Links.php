<?php
/*
 * @Description  : 友链
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\service\LinksService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("友链")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Links
{
    /**
     * @Apidoc\Title("友链列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\LinksModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data = LinksService::info('all');

        return success($data);
    }
}
