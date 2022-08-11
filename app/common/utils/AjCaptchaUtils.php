<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\utils;

use think\facade\Config;
use Fastknife\Service\ClickWordCaptchaService;
use Fastknife\Service\BlockPuzzleCaptchaService;

/**
 * 行为验证码
 */
class AjCaptchaUtils
{
    // 配置
    protected $config;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->config = Config::get('ajcaptcha');
    }

    /**
     * 获取验证码
     * 
     * @param string $captchaType 1||blockPuzzle 滑动验证码，2||clickWord 文字验证码
     *
     * @return array
     */
    public function get($captchaType)
    {
        if ($captchaType == 1 || $captchaType == 'blockPuzzle') {
            $service = new BlockPuzzleCaptchaService($this->config);
        } else {
            $service = new ClickWordCaptchaService($this->config);
        }
        $data = $service->get();

        return [
            'error' => false,
            'repCode' => '0000',
            'repData' => $data,
            'repMsg' => null,
            'success' => true,
        ];
    }

    /**
     * 验证验证码
     *
     * @param string $captchaType 1||blockPuzzle 滑动验证码，2||clickWord 文字验证码
     * @param array  $captchaData 验证码数据
     *
     * @return bool
     */
    public function check($captchaType, $captchaData)
    {
        $msg = null;
        $error = false;
        $repCode = '0000';
        try {
            if ($captchaType == 1 || $captchaType == 'blockPuzzle') {
                $service = new BlockPuzzleCaptchaService($this->config);
            } else {
                $service = new ClickWordCaptchaService($this->config);
            }
            $service->check($captchaData['token'], $captchaData['pointJson']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $error = true;
            $repCode = '6111';
        }

        return [
            'error' => $error,
            'repCode' => $repCode,
            'repData' => null,
            'repMsg' => $msg,
            'success' => !$error,
        ];
    }
}
