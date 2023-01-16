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
use app\common\validate\content\TagValidate;
use app\common\service\content\TagService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容标签")
 * @Apidoc\Group("content")
 * @Apidoc\Sort("300")
 */
class Tag extends BaseController
{
    /**
     * @Apidoc\Title("内容标签列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\content\TagModel", type="array", desc="标签列表", field="tag_id,tag_name,tag_desc,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = TagService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签信息")
     * @Apidoc\Param(ref="app\common\model\content\TagModel", field="tag_id")
     * @Apidoc\Returned(ref="app\common\model\content\TagModel")
     */
    public function info()
    {
        $param['tag_id'] = $this->request->param('tag_id/d', 0);

        validate(TagValidate::class)->scene('info')->check($param);

        $data = TagService::info($param['tag_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\TagModel", field="tag_name,tag_desc,sort")
     */
    public function add()
    {
        $param['tag_name'] = $this->request->param('tag_name/s', '');
        $param['tag_desc'] = $this->request->param('tag_desc/s', '');
        $param['sort']     = $this->request->param('sort/d', 250);

        validate(TagValidate::class)->scene('add')->check($param);

        $data = TagService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\TagModel", field="tag_id,tag_name,tag_desc,sort")
     */
    public function edit()
    {
        $param['tag_id']   = $this->request->param('tag_id/d', 0);
        $param['tag_name'] = $this->request->param('tag_name/s', '');
        $param['tag_desc'] = $this->request->param('tag_desc/s', '');
        $param['sort']     = $this->request->param('sort/d', 250);

        validate(TagValidate::class)->scene('edit')->check($param);

        $data = TagService::edit($param['tag_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(TagValidate::class)->scene('dele')->check($param);

        $data = TagService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\content\TagModel", field="is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->request->param('ids/a', []);
        $param['is_disable'] = $this->request->param('is_disable/d', 0);

        validate(TagValidate::class)->scene('disable')->check($param);

        $data = TagService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签内容")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="app\common\model\content\TagModel", field="tag_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\content\ContentModel", type="array", desc="内容列表", field="content_id,cover_id,name,unique,sort,hits,is_top,is_hot,is_rec,is_disable,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCoverUrlAttr"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getCategoryNamesAttr"),
     *   @Apidoc\Returned(ref="app\common\model\content\ContentModel\getTagNamesAttr"),
     * )
     */
    public function content()
    {
        $param['tag_id'] = $this->request->param('tag_id/d', 0);

        validate(TagValidate::class)->scene('content')->check($param);

        $where = $this->where(where_delete(['tag_ids', 'in', [$param['tag_id']]]));

        $data = TagService::content($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("内容标签内容解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\TagModel", field="tag_id")
     * @Apidoc\Param("content_ids", type="array", require=false, desc="内容id，为空则解除所有内容")
     */
    public function contentRemove()
    {
        $param['tag_id']      = $this->request->param('tag_id/a', []);
        $param['content_ids'] = $this->request->param('content_ids/a', []);

        validate(TagValidate::class)->scene('contentRemove')->check($param);

        $data = TagService::contentRemove($param['tag_id'], $param['content_ids']);

        return success($data);
    }
}
