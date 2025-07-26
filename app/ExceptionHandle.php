<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app;

use Throwable;
use think\Response;
use think\facade\Config;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\exception\HttpResponseException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use app\common\utils\ReturnCodeUtils;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制

        // 接口文档异常
        if ($e instanceof \hg\apidoc\exception\HttpException) {
            return json(['code' => $e->getCode(), 'message' => $e->getMessage()], $e->getStatusCode());
        }

        // 参数验证错误
        if ($e instanceof ValidateException) {
            $data['code'] = ReturnCodeUtils::ERROR;
            $data['msg']  = $e->getError();
            $data['data'] = [];
            return response($data, 200, [], 'json');
        }

        // 请求异常
        if ($e instanceof HttpException && $request->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode(), [], 'json');
        }

        // 其它异常
        $debug = Config::get('app.app_debug');
        $data['code'] = $e->getCode();
        $data['msg']  = $e->getMessage();
        $data['data'] = [];
        if ($debug) {
            $data['data'] = [
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'trace' => $e->getTrace(),
            ];
        }

        return response($data, 200, [], 'json');
    }
}
