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
use app\common\validate\setting\FeedbackValidate;
use app\common\service\setting\FeedbackService;
use app\common\service\setting\SettingService;
use app\common\cache\setting\FeedbackCache;

/**
 * @Apidoc\Title("lang(反馈)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("600")
 */
class Feedback extends BaseController
{
    /**
     * @Apidoc\Title("lang(反馈列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query("unique", type="string", default="", desc="编号，多个逗号隔开")
     * @Apidoc\Query(ref={FeedbackService::class,"edit"}, field="title")
     * @Apidoc\Query("create_time", type="array", default="", desc="提交时间")
     * @Apidoc\Returned(ref={FeedbackService::class,"basedata"})
     * @Apidoc\Returned("list", type="array", desc="反馈列表", children={
     *   @Apidoc\Returned(ref={FeedbackService::class,"info"}, field="feedback_id,unique,member_id,type,title,phone,email,remark,status,create_time,update_time,type_name,status_name,member_username"),
     * })
     */
    public function list()
    {
        $unique      = $this->param('unique/s', '');
        $title       = $this->param('title/s', '');
        $create_time = $this->param('create_time/a', []);

        $where = [['member_id', '=', member_id(true)]];
        if ($unique) {
            $where[] = ['unique', '=', $unique];
        }
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
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
        $where = where_disdel($where);

        $data = FeedbackService::list($where, $this->page(), $this->limit(), $this->order());
        $data['basedata'] = FeedbackService::basedata(false);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(反馈信息)")
     * @Apidoc\Query("feedback_id", type="string", require=true, desc="反馈id、编号")
     * @Apidoc\Returned(ref={FeedbackService::class,"info"})
     */
    public function info()
    {
        $param = $this->params(['feedback_id/s' => '']);

        validate(FeedbackValidate::class)->scene('info')->check($param);

        $data = FeedbackService::info($param['feedback_id'], false);

        if (is_numeric($param['feedback_id'])) {
            if (($data['member_id'] ?? '') != member_id()) {
                return error(lang('反馈不存在'));
            }
        }
        if (empty($data) || $data['is_disable'] || $data['is_delete']) {
            return error(lang('反馈不存在'));
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(反馈提交)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={FeedbackService::class,"edit"}, field="type,title,content,phone,email,images")
     * @Apidoc\Returned(ref={FeedbackService::class,"info"}, field="unique")
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

        $cache = new FeedbackCache();
        $cache_key = 'repeat' . $param['type'] . $param['phone'] . md5($param['title']);
        $cache_val = $cache->get($cache_key);
        if ($cache_val) {
            return error(lang('请勿重复提交'));
        } else {
            $cache->set($cache_key, 1, 60);
        }

        $data = FeedbackService::add($param);

        return success($data);
    }
}
