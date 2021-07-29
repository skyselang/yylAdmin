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
use app\common\service\cms\CommentService;
use app\common\cache\cms\CommentCache;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("留言")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Comment
{
    /**
     * @Apidoc\Title("留言")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\cms\CommentModel\add")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
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

        $comment = CommentCache::get($param['mobile']);
        if ($comment) {
            return error('请勿重复提交');
        } else {
            CommentCache::set($param['mobile'], $param['call'], 10);
        }

        $data = CommentService::add($param);

        return success($data);
    }
}
