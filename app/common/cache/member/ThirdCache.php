<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\member;

use app\common\cache\BaseCache;

/**
 * 会员第三方账号缓存
 */
class ThirdCache extends BaseCache
{
    /**
     * 缓存标签
     * @var string
     */
    public $tag = 'member_third';

    /**
     * 缓存前缀
     * @var string
     */
    protected $prefix = 'member_third:';

    /**
     * 缓存有效时间（秒，0永久）
     * @var int
     */
    protected $expire = 43200;

    /**
     * 构造函数
     * @return void
     */
    function __construct()
    {
        $this->tag($this->tag);
        $this->prefix($this->prefix);
        $this->expire($this->expire);
    }
}
