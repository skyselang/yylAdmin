<?php
/*
 * @Description  : 内容留言
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-10
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\CmsCommentValidate;
use app\common\service\CmsCommentService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("留言")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class CmsComment
{
    /**
     * @Apidoc\Title("留言")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\CmsCommentModel\add")
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

        validate(CmsCommentValidate::class)->scene('add')->check($param);

        $data = CmsCommentService::add($param);

        return success($data);
    }
}
