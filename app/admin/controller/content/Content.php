<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\content;

use app\common\controller\BaseController;
use app\common\validate\content\ContentValidate;
use app\common\service\content\ContentService;
use app\common\service\content\CategoryService;
use app\common\service\content\TagService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容管理")
 * @Apidoc\Group("content")
 * @Apidoc\Sort("100")
 */
class Content extends BaseController
{
    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="内容列表", children={
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel", field="content_id,image_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getImageUrlAttr", field="image_url"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr", field="category_names"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr", field="tag_names"),
     * })
     * @Apidoc\Returned("category", ref="app\common\model\content\CategoryModel", type="tree", desc="分类树形", field="category_id,category_pid,category_name")
     * @Apidoc\Returned("tag", ref="app\common\model\content\TagModel", type="array", desc="标签列表", field="tag_id,tag_name")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = ContentService::list($where, $this->page(), $this->limit(), $this->order());

        $data['category'] = CategoryService::list('tree', [where_delete()], [], 'category_id,category_pid,category_name');
        $data['tag']      = TagService::list([where_delete()], 0, 0, [], 'tag_id,tag_name')['list'] ?? [];
        $data['exps']     = where_exps();
        $data['where']    = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Query(ref="app\common\model\content\ContentModel", field="content_id")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getImageUrlAttr")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryIdsAttr")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagIdsAttr")
     * @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr")
     * @Apidoc\Returned(ref="imagesReturn")
     * @Apidoc\Returned(ref="videosReturn")
     * @Apidoc\Returned(ref="audiosReturn")
     * @Apidoc\Returned(ref="wordsReturn")
     * @Apidoc\Returned(ref="othersReturn")
     */
    public function info()
    {
        $param = $this->params(['content_id/d' => '']);

        validate(ContentValidate::class)->scene('info')->check($param);

        $data = ContentService::info($param['content_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel\getCategoryIdsAttr", field="category_ids")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel\getTagIdsAttr", field="tag_ids")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="unique,image_id,name,release_time,title,keywords,description,content,source,author,url,remark,sort,hits_initial")
     * @Apidoc\Param(ref="imagesParam")
     * @Apidoc\Param(ref="videosParam")
     * @Apidoc\Param(ref="audiosParam")
     * @Apidoc\Param(ref="wordsParam")
     * @Apidoc\Param(ref="othersParam")
     */
    public function add()
    {
        $param = $this->params(ContentService::$edit_field);

        validate(ContentValidate::class)->scene('add')->check($param);

        $data = ContentService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel\getCategoryIdsAttr", field="category_ids")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel\getTagIdsAttr", field="tag_ids")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="content_id,unique,image_id,name,release_time,title,keywords,description,content,source,author,url,remark,sort,hits_initial")
     * @Apidoc\Param(ref="imagesParam")
     * @Apidoc\Param(ref="videosParam")
     * @Apidoc\Param(ref="audiosParam")
     * @Apidoc\Param(ref="wordsParam")
     * @Apidoc\Param(ref="othersParam")
     */
    public function edit()
    {
        $param = $this->params(ContentService::$edit_field);

        validate(ContentValidate::class)->scene('edit')->check($param);

        $data = ContentService::edit($param['content_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(ContentValidate::class)->scene('dele')->check($param);

        $data = ContentService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改分类")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel\getCategoryIdsAttr", field="category_ids")
     */
    public function editcate()
    {
        $param = $this->params(['ids/a' => [], 'category_ids/a' => []]);

        validate(ContentValidate::class)->scene('editcate')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改标签")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel\getTagIdsAttr", field="tag_ids")
     */
    public function edittag()
    {
        $param = $this->params(['ids/a' => [], 'tag_ids/a' => []]);

        validate(ContentValidate::class)->scene('edittag')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="is_top")
     */
    public function istop()
    {
        $param = $this->params(['ids/a' => [], 'is_top/d' => 0]);

        validate(ContentValidate::class)->scene('istop')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="is_hot")
     */
    public function ishot()
    {
        $param = $this->params(['ids/a' => [], 'is_hot/d' => 0]);

        validate(ContentValidate::class)->scene('ishot')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="is_rec")
     */
    public function isrec()
    {
        $param = $this->params(['ids/a' => [], 'is_rec/d' => 0]);

        validate(ContentValidate::class)->scene('isrec')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(ContentValidate::class)->scene('disable')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容发布时间")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\ContentModel", field="release_time")
     */
    public function release()
    {
        $param = $this->params(['ids/a' => [], 'release_time/s' => null]);

        validate(ContentValidate::class)->scene('release')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }
}
