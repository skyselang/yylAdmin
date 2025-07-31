<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\system;

use app\common\cache\BaseCache;

/**
 * 菜单管理缓存
 */
class MenuCache extends BaseCache
{
    /**
     * 缓存标签
     * @var string
     */
    public $tag = 'system_menu';

    /**
     * 缓存前缀
     * @var string
     */
    protected $prefix = 'system_menu:';

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
