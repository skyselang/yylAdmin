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
use app\common\service\content\TagService;
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
     * @Apidoc\Returned("list", type="tree", desc="分类树形", children={
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel", field="category_id,category_pid,category_name,category_unique"), 
     *   @Apidoc\Returned(ref="app\common\model\content\CategoryModel\getCoverUrlAttr", field="cover_url"),
     * })
     */
    public function category()
    {
        $where = where_disdel();
        $order = $this->order(['sort' => 'desc', 'category_id' => 'desc']);
        $field = 'category_id,category_pid,category_name,category_unique,cover_id';

        $data = CategoryService::list('tree', $where, $order, $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("标签列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\content\TagModel", field="tag_id,tag_name,tag_unique")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\content\TagModel", type="array", desc="标签列表", field="tag_id,tag_name,tag_unique")
     */
    public function tag()
    {
        $tag_id     = $this->param('tag_id/s', '');
        $tag_name   = $this->param('tag_name/s', '');
        $tag_unique = $this->param('tag_unique/s', '');

        if ($tag_id) {
            $where[] = ['tag_id', 'in', $tag_id];
        }
        if ($tag_name) {
            $where[] = ['tag_name', 'like', '%' . $tag_name . '%'];
        }
        if ($tag_unique) {
            $where[] = ['tag_unique', 'in', $tag_unique];
        }
        $where[] = where_disable();
        $where[] = where_delete();

        $order = ['sort' => 'desc', 'tag_id' => 'desc'];
        $field = 'tag_id,tag_name,tag_unique';

        $data = TagService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\content\CategoryModel", field="category_id,category_unique")
     * @Apidoc\Query(ref="app\common\model\content\TagModel", field="tag_id,tag_unique")
     * @Apidoc\Query(ref="app\common\model\content\ContentModel", field="keywords")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="内容列表", children={
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,cover_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCoverUrlAttr", field="cover_url"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr", field="category_names"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr", field="tag_names")
     * })
     */
    public function list()
    {
        $category_id     = $this->param('category_id/s', '');
        $category_unique = $this->param('category_unique/s', '');
        $tag_id          = $this->param('tag_id/s', '');
        $tag_unique      = $this->param('tag_unique/s', '');
        $keywords        = $this->param('keywords/s', '');

        if ($category_id !== '') {
            $category_ids = explode(',', $category_id);
        }
        if ($category_unique !== '') {
            $category = CategoryService::info($category_unique, false);
            $category_ids[] = $category['category_id'] ?? '-1';
        }
        if ($category_id !== '' || $category_unique !== '') {
            $where[] = ['category_ids', 'in', $category_ids];
        }

        if ($tag_id !== '') {
            $tag_ids = explode(',', $tag_id);
        }
        if ($tag_unique !== '') {
            $tag = TagService::info($tag_unique, false);
            $tag_ids[] = $tag['tag_id'] ?? '-1';
        }
        if ($tag_id !== '' || $tag_unique !== '') {
            $where[] = ['tag_ids', 'in', $tag_ids];
        }

        if ($keywords) {
            $where[] = ['name|title|keywords', 'like', '%' . $keywords . '%'];
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
     * @Apidoc\Returned("prev_info", type="object", desc="上一条", children={
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,name")
     * })
     * @Apidoc\Returned("next_info", type="object", desc="下一条", children={
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,name")
     * })
     */
    public function info()
    {
        $param = $this->params(['content_id/s' => '', 'is_cate/d' => 0]);

        validate(ContentValidate::class)->scene('info')->check($param);

        $data = ContentService::info($param['content_id'], false);
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error([], '内容不存在');
        }

        $data['prev_info'] = ContentService::prev($data['content_id'], $param['is_cate']);
        $data['next_info'] = ContentService::next($data['content_id'], $param['is_cate']);

        return success($data);
    }
}
