<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\utils;

/**
 * 返回码
 */
class ReturnCodeUtils
{
    /**
     * 操作成功
     * @var integer
     */
    const SUCCESS = 200;
    /**
     * 操作失败
     * @var integer
     */
    const ERROR = 400;
    /**
     * 登录已失效，请重新登录
     * @var integer
     */
    const LOGIN_INVALID = 401;
    /**
     * 第三方账号未注册
     * @var integer
     */
    const THIRD_UNREGISTERED = 402;
    /**
     * 你没有权限操作
     * @var integer
     */
    const NO_PERMISSION = 403;
    /**
     * 接口地址错误
     * @var integer
     */
    const API_URL_ERROR = 404;
    /**
     * 你的操作过于频繁
     * @var integer
     */
    const FREQUENT_OPERATION = 429;
    /**
     * 服务器错误
     * @var integer
     */
    const SERVER_ERROR = 500;
}
