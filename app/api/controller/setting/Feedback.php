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
use app\common\cache\setting\FeedbackCache;
use app\common\service\setting\FeedbackService;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("反馈")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("900")
 */
class Feedback extends BaseController
{
    /**
     * @Apidoc\Title("反馈提交")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\FeedbackModel", field="type,title,content,phone,email")
     * @Apidoc\Param(ref="imagesParam")
     */
    public function add()
    {
        $setting = SettingService::info();
        if (!$setting['is_feedback']) {
            exception('系统模块（反馈）维护中...');
        }

        $param['type']    = $this->request->param('type/d', 0);
        $param['title']   = $this->request->param('title/s', '');
        $param['content'] = $this->request->param('content/s', '');
        $param['phone']   = $this->request->param('phone/s', '');
        $param['email']   = $this->request->param('email/s', '');
        $param['images']  = $this->request->param('images/a', []);

        validate(FeedbackValidate::class)->scene('add')->check($param);

        $feedback_key = 'repeat' . $param['type'] . $param['phone'] . md5($param['title']);
        $feedback_val = FeedbackCache::get($feedback_key);
        if ($feedback_val) {
            exception('请勿重复提交！');
        } else {
            FeedbackCache::set($feedback_key, $param['title'], 60);
        }

        $data = FeedbackService::add($param);

        return success($data);
    }
}
