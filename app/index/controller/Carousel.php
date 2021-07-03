<?php
/*
 * @Description  : 轮播
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\service\CarouselService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("轮播")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Carousel
{
    /**
     * @Apidoc\Title("轮播列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\CarouselModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data = CarouselService::info('all');

        return success($data);
    }
}
