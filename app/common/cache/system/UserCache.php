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
 * 用户管理缓存
 */
class UserCache extends BaseCache
{
    // 缓存标签
    public $tag = 'system_user';
    
    // 缓存前缀
    protected $prefix = 'system_user:';

    // 缓存有效时间（秒，0永久）
    protected $expire = 43200;

    // 构造函数
    function __construct()
    {
        $this->tag($this->tag);
        $this->prefix($this->prefix);
        $this->expire($this->expire);
    }
}
