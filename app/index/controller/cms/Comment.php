<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 留言控制器
namespace app\index\controller\cms;

use think\facade\Request;
use app\common\validate\cms\CommentValidate;
use app\common\cache\cms\CommentCache;
use app\common\service\cms\CommentService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("留言")
 * @Apidoc\Sort("620")
 * @Apidoc\Group("cms")
 */
class Comment
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
        $param['call']    = Request::param('call/s', '');
        $param['mobile']  = Request::param('mobile/s', '');
        $param['tel']     = Request::param('tel/s', '');
        $param['email']   = Request::param('email/s', '');
        $param['qq']      = Request::param('qq/s', '');
        $param['wechat']  = Request::param('wechat/s', '');
        $param['title']   = Request::param('title/s', '');
        $param['content'] = Request::param('content/s', '');

        validate(CommentValidate::class)->scene('add')->check($param);

        $comment_key = 'rep' . $param['mobile'];
        $comment = CommentCache::get($comment_key);
        if ($comment) {
            exception('请稍后再试');
        } else {
            CommentCache::set($comment_key, $param['call'], 60);
        }

        $data = CommentService::add($param);

        return success($data);
    }
}
