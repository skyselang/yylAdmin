<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\cms;

use app\common\BaseController;
use app\common\validate\cms\ContentValidate;
use app\common\service\cms\ContentService;
use app\common\service\cms\CategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("310")
 */
class Content extends BaseController
{
    /**
     * @Apidoc\Title("内容列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\cms\ContentModel\listReturn", type="array", desc="内容列表", 
     *     @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\category_name")
     * )
     * @Apidoc\Returned("category", ref="app\common\model\cms\CategoryModel\treeReturn", type="tree", childrenField="children", desc="分类树形")
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'content_id,is_top,is_hot,is_rec,is_hide,category_id');

        $data = ContentService::list($where, $this->page(), $this->limit(), $this->order());
        $data['category'] = CategoryService::list('tree', [['is_delete', '=', 0]], [], 'category_id,category_pid,category_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容信息")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\id")
     * @Apidoc\Returned(ref="app\common\model\cms\ContentModel\infoReturn")
     * @Apidoc\Returned(ref="imgsReturn")
     * @Apidoc\Returned(ref="filesReturn")
     * @Apidoc\Returned(ref="videosReturn")
     */
    public function info()
    {
        $param['content_id'] = $this->param('content_id/d', '');

        validate(ContentValidate::class)->scene('info')->check($param);

        $data = ContentService::info($param['content_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\addParam")
     * @Apidoc\Param("name", mock="@ctitle(9, 31)")
     * @Apidoc\Param("content", mock="@cparagraph")
     * @Apidoc\Param(ref="imgsParam")
     * @Apidoc\Param(ref="filesParam")
     * @Apidoc\Param(ref="videosParam")
     */
    public function add()
    {
        $param['category_id'] = $this->param('category_id/d', 0);
        $param['name']        = $this->param('name/s', '');
        $param['title']       = $this->param('title/s', '');
        $param['keywords']    = $this->param('keywords/s', '');
        $param['description'] = $this->param('description/s', '');
        $param['img_id']      = $this->param('img_id/d', 0);
        $param['author']      = $this->param('author/s', '');
        $param['url']         = $this->param('url/s', '');
        $param['imgs']        = $this->param('imgs/a', []);
        $param['files']       = $this->param('files/a', []);
        $param['videos']      = $this->param('videos/a', []);
        $param['sort']        = $this->param('sort/d', 250);
        $param['content']     = $this->param('content/s', '');

        validate(ContentValidate::class)->scene('add')->check($param);

        $param['img_ids']   = file_ids($param['imgs']);
        $param['file_ids']  = file_ids($param['files']);
        $param['video_ids'] = file_ids($param['videos']);
        $data = ContentService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\editParam")
     * @Apidoc\Param(ref="imgsParam")
     * @Apidoc\Param(ref="filesParam")
     * @Apidoc\Param(ref="videosParam")
     */
    public function edit()
    {
        $param['content_id']  = $this->param('content_id/d', '');
        $param['category_id'] = $this->param('category_id/d', 0);
        $param['name']        = $this->param('name/s', '');
        $param['title']       = $this->param('title/s', '');
        $param['keywords']    = $this->param('keywords/s', '');
        $param['description'] = $this->param('description/s', '');
        $param['img_id']      = $this->param('img_id/d', 0);
        $param['author']      = $this->param('author/s', '');
        $param['url']         = $this->param('url/s', '');
        $param['imgs']        = $this->param('imgs/a', []);
        $param['files']       = $this->param('files/a', []);
        $param['videos']      = $this->param('videos/a', []);
        $param['sort']        = $this->param('sort/d', 250);
        $param['content']     = $this->param('content/s', '');

        validate(ContentValidate::class)->scene('edit')->check($param);

        $param['img_ids']   = file_ids($param['imgs']);
        $param['file_ids']  = file_ids($param['files']);
        $param['video_ids'] = file_ids($param['videos']);
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
        $param['ids'] = $this->param('ids/a', '');

        validate(ContentValidate::class)->scene('dele')->check($param);

        $data = ContentService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容修改分类")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\category_id")
     */
    public function cate()
    {
        $param['ids']         = $this->param('ids/a', '');
        $param['category_id'] = $this->param('category_id/d', 0);

        validate(ContentValidate::class)->scene('cate')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否置顶")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_top")
     */
    public function istop()
    {
        $param['ids']    = $this->param('ids/a', '');
        $param['is_top'] = $this->param('is_top/d', 0);

        validate(ContentValidate::class)->scene('istop')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否热门")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_hot")
     */
    public function ishot()
    {
        $param['ids']    = $this->param('ids/a', '');
        $param['is_hot'] = $this->param('is_hot/d', 0);

        validate(ContentValidate::class)->scene('ishot')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否推荐")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_rec")
     */
    public function isrec()
    {
        $param['ids']    = $this->param('ids/a', '');
        $param['is_rec'] = $this->param('is_rec/d', 0);

        validate(ContentValidate::class)->scene('isrec')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\cms\ContentModel\is_hide")
     */
    public function ishide()
    {
        $param['ids']     = $this->param('ids/a', '');
        $param['is_hide'] = $this->param('is_hide/d', 0);

        validate(ContentValidate::class)->scene('ishide')->check($param);

        $data = ContentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\cms\ContentModel\listReturn", type="array", desc="内容列表",
     *    @Apidoc\Returned(ref="app\common\model\cms\CategoryModel\category_name")
     * )
     * @Apidoc\Returned("category", ref="app\common\model\cms\CategoryModel\treeReturn", type="tree", childrenField="children", desc="分类树形")
     */
    public function recover()
    {
        $where = $this->where(['is_delete', '=', 1], 'content_id,is_top,is_hot,is_rec,is_hide,category_id');

        $data = ContentService::list($where, $this->page(), $this->limit(), $this->order());
        $data['category'] = CategoryService::list('tree', [['is_delete', '=', 0]], [], 'category_id,category_pid,category_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(ContentValidate::class)->scene('reco')->check($param);

        $data = ContentService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(ContentValidate::class)->scene('dele')->check($param);

        $data = ContentService::dele($param['ids'], true);

        return success($data);
    }
}
