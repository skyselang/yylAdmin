<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\cms;

use app\common\BaseController;
use app\common\validate\cms\CommentValidate;
use app\common\cache\cms\CommentCache;
use app\common\service\cms\CommentService;
use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("留言")
 * @Apidoc\Group("cms")
 * @Apidoc\Sort("620")
 */
class Comment extends BaseController
{
    /**
     * @Apidoc\Title("留言")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\addParam")
     * @Apidoc\Param("call", mock="@cname")
     * @Apidoc\Param("title", mock="@ctitle(9, 31)")
     * @Apidoc\Param("content", mock="@cparagraph")
     * @Apidoc\Param("mobile", mock="@phone")
     */
    public function add()
    {
        $setting = SettingService::info();
        if (!$setting['is_comment']) {
            exception('功能维护中...');
        }

        $param['call']    = $this->param('call/s', '');
        $param['mobile']  = $this->param('mobile/s', '');
        $param['tel']     = $this->param('tel/s', '');
        $param['email']   = $this->param('email/s', '');
        $param['qq']      = $this->param('qq/s', '');
        $param['wechat']  = $this->param('wechat/s', '');
        $param['title']   = $this->param('title/s', '');
        $param['content'] = $this->param('content/s', '');

        validate(CommentValidate::class)->scene('add')->check($param);

        $comment_key = 'rep' . $param['mobile'];
        $comment = CommentCache::get($comment_key);
        if ($comment) {
            exception('系统繁忙，请稍后再试');
        } else {
            CommentCache::set($comment_key, $param['call'], 60);
        }

        $data = CommentService::add($param);

        return success($data);
    }
}
