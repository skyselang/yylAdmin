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
use app\common\validate\cms\CommentValidate;
use app\common\service\cms\CommentService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("留言管理")
 * @Apidoc\Group("adminCms")
 * @Apidoc\Sort("330")
 */
class Comment extends BaseController
{
    /**
     * @Apidoc\Title("留言列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\cms\CommentModel\listReturn", type="array", desc="留言列表")
     */
    public function list()
    {
        $where = $this->where(['is_delete', '=', 0], 'comment_id,is_unread');

        $data = CommentService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("留言信息")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\id")
     * @Apidoc\Returned(ref="app\common\model\cms\CommentModel\infoReturn")
     */
    public function info()
    {
        $param['comment_id'] = $this->param('comment_id/d', '');

        validate(CommentValidate::class)->scene('info')->check($param);

        $data = CommentService::info($param['comment_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\addParam")
     * @Apidoc\Param("call", mock="@cname")
     * @Apidoc\Param("mobile", mock="@phone")
     * @Apidoc\Param("title", mock="@ctitle(8, 32)")
     * @Apidoc\Param("content", mock="@cparagraph(8, 32)")
     */
    public function add()
    {
        $param['call']    = $this->param('call/s', '');
        $param['mobile']  = $this->param('mobile/s', '');
        $param['tel']     = $this->param('tel/s', '');
        $param['email']   = $this->param('email/s', '');
        $param['qq']      = $this->param('qq/s', '');
        $param['wechat']  = $this->param('wechat/s', '');
        $param['title']   = $this->param('title/s', '');
        $param['content'] = $this->param('content/s', '');
        $param['remark']  = $this->param('remark/s', '');

        validate(CommentValidate::class)->scene('add')->check($param);

        $data = CommentService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\editParam")
     */
    public function edit()
    {
        $param['comment_id'] = $this->param('comment_id/d', '');
        $param['call']       = $this->param('call/s', '');
        $param['mobile']     = $this->param('mobile/s', '');
        $param['tel']        = $this->param('tel/s', '');
        $param['email']      = $this->param('email/s', '');
        $param['qq']         = $this->param('qq/s', '');
        $param['wechat']     = $this->param('wechat/s', '');
        $param['title']      = $this->param('title/s', '');
        $param['content']    = $this->param('content/s', '');
        $param['remark']     = $this->param('remark/s', '');

        validate(CommentValidate::class)->scene('edit')->check($param);

        $data = CommentService::edit($param['comment_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言已读")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function isread()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(CommentValidate::class)->scene('isread')->check($param);

        $param['is_unread'] = 0;
        $param['read_time'] = datetime();
        $data = CommentService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\cms\CommentModel\listReturn", type="array", desc="留言列表")
     */
    public function recover()
    {
        $where = $this->where(['is_delete', '=', 1], 'comment_id,is_unread');

        $data = CommentService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(CommentValidate::class)->scene('reco')->check($param);

        $data = CommentService::edit($param['ids'], ['is_delete' => 0]);

        return success($data);
    }

    /**
     * @Apidoc\Title("留言回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(CommentValidate::class)->scene('dele')->check($param);

        $data = CommentService::dele($param['ids'], true);

        return success($data);
    }
}
