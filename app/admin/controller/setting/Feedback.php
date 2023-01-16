<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\setting\FeedbackValidate;
use app\common\service\setting\FeedbackService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("反馈管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("400")
 */
class Feedback extends BaseController
{
    /**
     * @Apidoc\Title("反馈列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\setting\FeedbackModel", type="array", desc="反馈列表", field="feedback_id,type,title,phone,email,remark,is_unread,create_time,update_time",
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getTypeNameAttr")
     * )
     * @Apidoc\Returned("types", type="array", desc="反馈类型")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = FeedbackService::list($where, $this->page(), $this->limit(), $this->order());

        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈信息")
     * @Apidoc\Query(ref="app\common\model\setting\FeedbackModel", field="feedback_id")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel")
     * @Apidoc\Returned(ref="imagesReturn")
     */
    public function info()
    {
        $param['feedback_id'] = $this->request->param('feedback_id/d', 0);

        validate(FeedbackValidate::class)->scene('info')->check($param);

        $data = FeedbackService::info($param['feedback_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="type,title,content,phone,email,remark")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function add()
    {
        $param['type']       = $this->request->param('type/d', 0);
        $param['title']      = $this->request->param('title/s', '');
        $param['content']    = $this->request->param('content/s', '');
        $param['phone']      = $this->request->param('phone/s', '');
        $param['email']      = $this->request->param('email/s', '');
        $param['remark']     = $this->request->param('remark/s', '');
        $param['images']     = $this->request->param('images/a', []);
        $param['create_uid'] = user_id();

        validate(FeedbackValidate::class)->scene('add')->check($param);

        $data = FeedbackService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="feedback_id,type,title,content,phone,email,remark")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function edit()
    {
        $param['feedback_id'] = $this->request->param('feedback_id/d', 0);
        $param['type']        = $this->request->param('type/d', 0);
        $param['title']       = $this->request->param('title/s', '');
        $param['content']     = $this->request->param('content/s', '');
        $param['phone']       = $this->request->param('phone/s', '');
        $param['email']       = $this->request->param('email/s', '');
        $param['remark']      = $this->request->param('remark/s', '');
        $param['images']      = $this->request->param('images/a', []);

        validate(FeedbackValidate::class)->scene('edit')->check($param);

        $data = FeedbackService::edit($param['feedback_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->request->param('ids/a', []);

        validate(FeedbackValidate::class)->scene('dele')->check($param);

        $data = FeedbackService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈是否未读")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="is_unread")
     */
    public function unread()
    {
        $param['ids']       = $this->request->param('ids/a', []);
        $param['is_unread'] = $this->request->param('is_unread/d', 0);

        validate(FeedbackValidate::class)->scene('readed')->check($param);

        $data = FeedbackService::edit($param['ids'], $param);

        return success($data);
    }
}
