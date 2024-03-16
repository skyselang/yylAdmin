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
use app\common\validate\setting\FeedbackValidate;
use app\common\service\setting\FeedbackService;
use app\common\service\setting\SettingService;
use app\common\cache\setting\FeedbackCache;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("反馈")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("900")
 */
class Feedback extends BaseController
{
    /**
     * @Apidoc\Title("反馈列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Query(ref="app\common\model\member\LogModel", field="title,create_time")
     * @Apidoc\Returned("list", type="array", desc="反馈列表", children={
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel", field="feedback_id,member_id,type,title,phone,email,remark,status,receipt_no,create_time,update_time"),
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getTypeNameAttr", field="type_name"),
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getStatusNameAttr", field="status_name"),
     *   @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getMemberUsernameAttr", field="member_username")
     * })
     * @Apidoc\Returned("types", type="array", desc="反馈类型")
     */
    public function list()
    {
        $title       = $this->param('title/s', '');
        $create_time = $this->param('create_time/a', []);

        $where[] = ['member_id', '=', member_id(true)];
        if ($title !== '') {
            $where[] = ['title|receipt_no', 'like', '%' . $title . '%'];
        }
        if ($create_time) {
            $start_date = $create_time[0] ?? '';
            $end_date   = $create_time[1] ?? '';
            if ($start_date) {
                $where[] = ['create_time', '>=', $start_date . ' 00:00:00'];
            }
            if ($end_date) {
                $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
            }
        }
        $where[] = where_disable();
        $where[] = where_delete();

        $data = FeedbackService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈信息")
     * @Apidoc\Query("feedback_id", type="string", require=true, desc="反馈id、回执编号")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getTypeNameAttr")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getStatusNameAttr")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel\getMemberUsernameAttr")
     * @Apidoc\Returned(ref="imagesReturn")
     */
    public function info()
    {
        $param = $this->params(['feedback_id/s' => '']);

        validate(FeedbackValidate::class)->scene('info')->check($param);

        $data = FeedbackService::info($param['feedback_id'], false);

        if (is_numeric($param['feedback_id'])) {
            if (($data['member_id'] ?? '') != member_id()) {
                return error('反馈不存在');
            }
        }
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error('反馈不存在');
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("反馈提交")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="type,title,content,phone,email")
     * @Apidoc\Param(ref="imagesParam")
     * @Apidoc\Returned(ref="app\common\model\setting\FeedbackModel", field="receipt_no")
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $data['types'] = SettingService::feedbackTypes();
            return success($data);
        }

        $param = $this->params([
            'type/d'    => 0,
            'title/s'   => '',
            'content/s' => '',
            'phone/s'   => '',
            'email/s'   => '',
            'images/a'  => [],
        ]);
        $param['member_id'] = member_id();

        validate(FeedbackValidate::class)->scene('add')->check($param);

        $feedback_key = 'repeat' . $param['type'] . $param['phone'] . md5($param['title']);
        $feedback_val = FeedbackCache::get($feedback_key);
        if ($feedback_val) {
            return error('请勿重复提交！');
        } else {
            FeedbackCache::set($feedback_key, $param['title'], 60);
        }

        $data = FeedbackService::add($param);

        return success($data);
    }
}
