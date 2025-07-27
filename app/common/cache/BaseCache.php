<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache;

use think\facade\Cache;

/**
 * 缓存基类
 */
class BaseCache
{
    /**
     * 缓存标签
     * @var string
     */
    public $tag;

    /**
     * 缓存前缀
     * @var string
     */
    protected $prefix;

    /**
     * 缓存有效时间（秒，0永久）
     * @var int|null
     */
    protected $expire = 30 * 24 * 60 * 60;

    /**
     * 是否允许清除缓存（调用系统清除缓存方法时）
     * @var bool
     */
    public $allowClear = true;

    /**
     * 设置缓存标签
     * @param string $tag
     */
    protected function tag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * 设置缓存前缀
     * @param string $prefix
     */
    protected function prefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * 设置缓存有效时间
     * @param int|null $expire 有效时间（秒，0永久）
     */
    protected function expire($expire)
    {
        if ($expire === null) {
            $this->expire = config('cache.expire', 0);
        } else {
            $this->expire = $expire;
        }
    }

    /**
     * 设置是否允许清除缓存
     * @param bool $allowClear
     */
    protected function allowClear($allowClear)
    {
        $this->allowClear = $allowClear;
    }

    /**
     * 缓存键名
     * @param mixed $key 缓存key
     * @return string
     */
    public function key($key)
    {
        return $this->prefix . $key;
    }

    /**
     * 设置缓存
     * @param mixed    $key    缓存key
     * @param mixed    $data   缓存数据
     * @param int|null $expire 有效时间（秒，0永久）
     */
    public function set($key, $data, $expire = null)
    {
        if ($expire === null) {
            $expire = $this->expire;
        }
        return Cache::tag($this->tag)->set($this->key($key), $data, $expire);
    }

    /**
     * 缓存自增
     * @param mixed $key  缓存key
     * @param int   $step 步长
     */
    public function inc($key, $step = 1)
    {
        return Cache::inc($this->key($key), $step);
    }

    /**
     * 缓存自减
     * @param mixed $key  缓存key
     * @param int   $step 步长
     */
    public function dec($key, $step = 1)
    {
        return Cache::dec($this->key($key), $step);
    }

    /**
     * 获取缓存
     * @param mixed $key     缓存key
     * @param mixed $default 默认值
     */
    public function get($key, $default = null)
    {
        return Cache::get($this->key($key), $default);
    }

    /**
     * 追加一个缓存数据
     * @param mixed $key   缓存key
     * @param mixed $value 数据
     */
    public function push($key, $value)
    {
        return Cache::push($this->key($key), $value);
    }

    /**
     * 删除缓存
     * @param mixed $key 缓存key
     */
    public function del($key)
    {
        return $this->delete($key);
    }

    /**
     * 删除缓存
     * @param mixed $key 缓存key
     */
    public function delete($key)
    {
        $keys = var_to_array($key);
        foreach ($keys as $k) {
            Cache::delete($this->key($k));
        }
        return true;
    }

    /**
     * 获取并删除缓存
     * @param mixed $key     缓存key
     * @param mixed $default 默认值
     */
    public function pull($key, $default = null)
    {
        return Cache::pull($this->key($key), $default);
    }

    /**
     * 清空缓存
     */
    public function clear()
    {
        return Cache::tag($this->tag)->clear();
    }

    /**
     * 不存在则写入缓存数据后返回
     * @param mixed $key   缓存key
     * @param mixed $value 数据
     */
    public function remember($key, $value)
    {
        return Cache::remember($this->key($key), $value);
    }

    /**
     * 获取缓存对象
     */
    public function handler()
    {
        return Cache::handler();
    }

    /**
     * 获取tp缓存类
     */
    public function cache()
    {
        return new Cache();
    }
}
