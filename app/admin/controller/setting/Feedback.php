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
     * @Apidoc\Returned("list", type="array", desc="反馈列表", children={
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel", field="feedback_id,member_id,type,title,phone,email,remark,status,is_disable,create_time,update_time"), 
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getTypeNameAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getStatusNameAttr"),
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getMemberUsernameAttr")
     * })
     * @Apidoc\Returned("types", type="array", desc="反馈类型")
     * @Apidoc\Returned("statuss", type="array", desc="反馈状态")
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
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getTypeNameAttr")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getStatusNameAttr")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getMemberUsernameAttr")
     * @Apidoc\Returned(ref="imagesReturn")
     */
    public function info()
    {
        $param = $this->params(['feedback_id/d' => '']);

        validate(FeedbackValidate::class)->scene('info')->check($param);

        $data = FeedbackService::info($param['feedback_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="member_id,receipt_no,type,title,phone,email,content,reply,remark")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function add()
    {
        $param = $this->params(FeedbackService::$edit_field);

        validate(FeedbackValidate::class)->scene('add')->check($param);

        $data = FeedbackService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="member_id,receipt_no,feedback_id,type,title,phone,email,content,replay,remark")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function edit()
    {
        $param = $this->params(FeedbackService::$edit_field);

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
        $param = $this->params(['ids/a' => []]);

        validate(FeedbackValidate::class)->scene('dele')->check($param);

        $data = FeedbackService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈状态")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="status")
     */
    public function status()
    {
        $param = $this->params(['ids/a' => [], 'status/d' => 0]);

        validate(FeedbackValidate::class)->scene('status')->check($param);

        $data = FeedbackService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(FeedbackValidate::class)->scene('disable')->check($param);

        $data = FeedbackService::edit($param['ids'], $param);

        return success($data);
    }
}
