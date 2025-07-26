<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$cache.namespace};

use app\common\cache\BaseCache;

/**
 * {$form.controller_title}缓存
 */
class {$cache.class_name} extends BaseCache
{
    // 缓存标签
    public $tag = '{$tables[0].table_name}';
    // 缓存前缀
    protected $prefix = '{$tables[0].table_name}:';
    // 缓存有效时间（秒，0永久）
    protected $expire = 43200;

    function __construct()
    {
        $this->tag($this->tag);
        $this->prefix($this->prefix);
        $this->expire($this->expire);
    }
}
