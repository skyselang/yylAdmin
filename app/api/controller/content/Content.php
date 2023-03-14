<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\content;

use app\common\controller\BaseController;
use app\common\validate\content\ContentValidate;
use app\common\service\content\CategoryService;
use app\common\service\content\ContentService;
use app\common\service\content\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容")
 * @Apidoc\Group("content")
 * @Apidoc\Sort("100")
 */
class Content extends BaseController
{
    // 设置
    protected $setting = [];

    /**
     * 初始化
     */
    public function initialize()
    {
        $setting = SettingService::info();
        $this->setting = $setting;
        if (!$setting['is_content']) {
            exception('系统模块（内容）维护中...');
            return;
        }
    }

    /**
     * @Apidoc\Title("分类列表")
     * @Apidoc\Returned("list", ref="app\common\model\content\CategoryModel", type="tree", desc="分类树形", field="category_id,category_pid,category_name",
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel\cover")
     * )
     */
    public function category()
    {
        $where = where_disdel();
        $order = $this->order(['sort' => 'desc', 'category_id' => 'desc']);
        $field = 'category_id,category_pid,category_name,cover_id';

        $data = CategoryService::list('tree', $where, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="category_id,name")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\content\ContentModel", type="array", desc="内容列表", field="content_id,cover_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel", field="category_name")
     * )
     */
    public function list()
    {
        $category_id = $this->request->param('category_id/s', '');
        $name        = $this->request->param('name/s', '');

        $where[] = ['content_id', '>', 0];
        if ($category_id !== '') {
            $where[] = ['category_id', '=', $category_id];
        }
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        $where[] = where_disable();
        $where[] = where_delete();

        $order = ['is_top' => 'desc', 'is_hot' => 'desc', 'is_rec' => 'desc', 'sort' => 'desc', 'content_id' => 'desc'];

        $data = ContentService::list($where, $this->page(), $this->limit(), $this->order($order));

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Query("content_id", type="string", require=true, default="", desc="内容id、标识")
     * @Apidoc\Query("is_cate", type="int", require=false, default="0", desc="上/下条是否当前内容分类")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCoverUrlAttr")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr")
     * @Apidoc\Returned(ref="imagesReturn")
     * @Apidoc\Returned(ref="videosReturn")
     * @Apidoc\Returned(ref="audiosReturn")
     * @Apidoc\Returned(ref="wordsReturn")
     * @Apidoc\Returned(ref="othersReturn")
     * @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,name")
     * )
     * @Apidoc\Returned("next_info", type="object", desc="下一条",
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,name")
     * )
     */
    public function info()
    {
        $param['content_id'] = $this->request->param('content_id/s', '');
        $param['is_cate']    = $this->request->param('is_cate/d', 0);

        validate(ContentValidate::class)->scene('info')->check($param);

        $data = ContentService::info($param['content_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return success([], '内容不存在或已禁用或已删除');
        }

        $data['prev_info'] = ContentService::prev($data['content_id'], $param['is_cate']);
        $data['next_info'] = ContentService::next($data['content_id'], $param['is_cate']);

        return success($data);
    }
}
