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
use app\common\service\setting\CarouselService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("轮播")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("700")
 */
class Carousel extends BaseController
{
    /**
     * @Apidoc\Title("轮播列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query("title", type="string", default="", desc="标题")
     * @Apidoc\Query("position", type="string", default="", desc="位置")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\CarouselModel", type="array", desc="轮播列表", field="carousel_id,file_id,file_type,title,link,position,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $title    = $this->request->param('title/s', '');
        $position = $this->request->param('position/s', '');

        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        if ($position) {
            $where[] = ['position', '=', $position];
        }
        $where[] = where_disable();
        $where[] = where_delete();

        $order = ['sort' => 'desc', 'carousel_id' => 'desc'];

        $data = CarouselService::list($where, $this->page(), $this->limit(), $this->order($order));

        return success($data);
    }
}
