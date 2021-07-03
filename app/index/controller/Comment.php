<?php
/*
 * @Description  : 留言
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\CommentValidate;
use app\common\service\CommentService;
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
     * @Apidoc\Param(ref="app\common\model\CommentModel\add")
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

        $data = CommentService::add($param);

        return success($data);
    }
}
