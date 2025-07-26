<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\setting\CarouselValidate;
use app\common\service\setting\CarouselService;

/**
 * @Apidoc\Title("lang(轮播)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("200")
 */
class Carousel extends BaseController
{
    /**
     * @Apidoc\Title("lang(轮播列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query("unique", type="string", default="", desc="编号，多个逗号隔开")
     * @Apidoc\Query(ref={CarouselService::class,"edit"}, field="title,position")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="轮播列表", children={
     *   @Apidoc\Returned(ref={CarouselService::class,"info"}, field="carousel_id,unique,file_id,title,url,position,desc,sort,is_disable,create_time,update_time,file_url,file_name,file_ext,file_type,file_type_name"),
     * })
     */
    public function list()
    {
        $unique   = $this->param('unique/s', '');
        $title    = $this->param('title/s', '');
        $position = $this->param('position/s', '');

        $where = [['carousel_id', '>', 0]];
        if ($unique) {
            $where[] = ['unique', 'in', $unique];
        }
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        if ($position) {
            $where[] = ['position', '=', $position];
        }
        $where = where_disdel($where);

        $order = ['sort' => 'desc', 'carousel_id' => 'desc'];

        $data = CarouselService::list($where, $this->page(), $this->limit(), $this->order($order));

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(轮播信息)")
     * @Apidoc\Query("carousel_id", type="string", require=true, default="", desc="轮播id、编号")
     * @Apidoc\Returned(ref={CarouselService::class,"info"})
     */
    public function info()
    {
        $param = $this->params(['carousel_id/s' => '']);

        validate(CarouselValidate::class)->scene('info')->check($param);

        $data = CarouselService::info($param['carousel_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error(lang('轮播不存在'));
        }

        return success($data);
    }
}
