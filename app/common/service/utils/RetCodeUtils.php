<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\utils;

/**
 * 返回码
 */
class RetCodeUtils
{
    /**
     * 操作成功
     * @var integer
     */
    const SUCCESS = 200;
    const SUCCESS_MSG = '操作成功';

    /**
     * 操作失败
     * @var integer
     */
    const ERROR = 400;
    const ERROR_MSG = '操作失败';
    /**
     * 登录已失效，请重新登录
     * @var integer
     */
    const LOGIN_INVALID = 401;
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

    /**
     * 返回码描述
     * @param int $code 返回码
     * @return array|string 返回码数组或描述
     */
    public static function codeMsg($code = -1)
    {
        $codes = [
            self::SUCCESS => '操作成功',
            self::ERROR => '操作失败',
            self::LOGIN_INVALID => '登录已失效，请重新登录',
            self::NO_PERMISSION => '你没有权限操作',
            self::API_URL_ERROR => '接口地址错误',
            self::FREQUENT_OPERATION => '你的操作过于频繁',
            self::SERVER_ERROR => '服务器错误',
        ];
        if ($code !== -1) {
            return $codes[$code] ?? '';
        }
        return $codes;
    }
}
