<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\utils;

use app\common\cache\BaseCache;

/**
 * 短信验证码缓存
 */
class CaptchaSmsCache extends BaseCache
{
    // 缓存标签
    public $tag = 'captcha_sms';

    // 缓存前缀
    protected $prefix = 'captcha_sms:';

    // 缓存有效时间（秒，0永久）
    protected $expire = 1800;

    // 是否允许清除缓存（调用系统清除缓存方法时）
    public $allowClear = false;

    // 构造函数
    function __construct()
    {
        $this->tag($this->tag);
        $this->prefix($this->prefix);
        $this->expire($this->expire);
        $this->allowClear($this->allowClear);
    }
}
