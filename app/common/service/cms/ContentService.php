<?php
/*
 * @Description  : 内容管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-16
 */

namespace app\common\service\cms;

use think\facade\Db;
use app\common\cache\cms\ContentCache;

class ContentService
{
    // 内容分类表名
    protected static $db_name = 'cms_content';

    /**
     * 内容列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'content_id,category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time,delete_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'content_id' => 'desc'];
        }

        $count = Db::name(self::$db_name)
            ->where($where)
            ->count('content_id');

        $list = Db::name(self::$db_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $category = CategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            if (isset($v['category_id'])) {
                foreach ($category as $kp => $vp) {
                    if ($v['category_id'] == $vp['category_id']) {
                        $list[$k]['category_name'] = $vp['category_name'];
                    }
                }
            }

            $list[$k]['img_url'] = '';
            if (isset($v['imgs'])) {
                $imgs = file_unser($v['imgs']);
                if ($imgs) {
                    $list[$k]['img_url'] = $imgs[0]['url'];
                }
                unset($list[$k]['imgs']);
            }
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 内容信息
     * 
     * @param $content_id 内容id
     * 
     * @return array|Exception
     */
    public static function info($content_id)
    {
        $content = ContentCache::get($content_id);

        if (empty($content)) {
            $where[] = ['content_id', '=', $content_id];
            $content = Db::name(self::$db_name)
                ->where($where)
                ->find();
            if (empty($content)) {
                exception('内容不存在：' . $content_id);
            }

            $category = CategoryService::info($content['category_id']);

            $content['category_name'] = $category['category_name'];
            $content['imgs']          = file_unser($content['imgs']);
            $content['files']         = file_unser($content['files']);
            $content['videos']        = file_unser($content['videos']);

            ContentCache::set($content_id, $content);
        }

        // 点击量
        $gate = 10;
        $key = $content['content_id'] . 'hits';
        $hits = ContentCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name(self::$db_name)
                    ->where('content_id', '=', $content['content_id'])
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    ContentCache::del($key);
                }
            } else {
                ContentCache::inc($key, 1);
            }
        } else {
            ContentCache::set($key, 1);
        }

        return $content;
    }

    /**
     * 内容添加
     *
     * @param $param 内容信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);
        $param['videos']      = file_ser($param['videos']);
        $param['create_time'] = datetime();

        $content_id = Db::name(self::$db_name)
            ->insertGetId($param);
        if (empty($content_id)) {
            exception();
        }

        $param['content_id'] = $content_id;
        $param['imgs']   = file_unser($param['imgs']);
        $param['files']  = file_unser($param['files']);
        $param['videos'] = file_unser($param['videos']);

        return $param;
    }

    /**
     * 内容修改 
     *     
     * @param $param 内容信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $content_id = $param['content_id'];

        unset($param['content_id']);

        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);
        $param['videos']      = file_ser($param['videos']);
        $param['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', $content_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ContentCache::del($content_id);

        $param['content_id'] = $content_id;

        return $param;
    }

    /**
     * 内容删除
     * 
     * @param array $content 内容列表
     * 
     * @return array|Exception
     */
    public static function dele($content)
    {
        $content_ids = array_column($content, 'content_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 内容是否置顶
     *
     * @param array $content 内容列表
     * @param int   $is_top  是否置顶
     * 
     * @return array
     */
    public static function istop($content, $is_top = 0)
    {
        $content_ids = array_column($content, 'content_id');

        $update['is_top']      = $is_top;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 内容是否热门
     *
     * @param array $content 内容列表
     * @param int   $is_hot  是否热门
     * 
     * @return array
     */
    public static function ishot($content, $is_hot = 0)
    {
        $content_ids = array_column($content, 'content_id');

        $update['is_hot']      = $is_hot;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 内容是否推荐
     *
     * @param array $content    内容信息
     * @param int   $is_rec 是否推荐
     * 
     * @return array
     */
    public static function isrec($content, $is_rec = 0)
    {
        $content_ids = array_column($content, 'content_id');

        $update['is_rec']      = $is_rec;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 内容是否隐藏
     *
     * @param array $content 内容列表
     * @param int   $is_hide 是否隐藏
     * 
     * @return array
     */
    public static function ishide($content, $is_hide = 0)
    {
        $content_ids = array_column($content, 'content_id');

        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 内容上一条
     *
     * @param integer $content_id  内容id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条内容
     */
    public static function prev($content_id, $is_category = 0)
    {
        $where[] = ['content_id', '<', $content_id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $content = self::info($content_id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $content = Db::name(self::$db_name)
            ->field('content_id,name')
            ->where($where)
            ->order('content_id', 'desc')
            ->find();
        if (empty($content)) {
            return [];
        }

        return $content;
    }

    /**
     * 内容下一条
     *
     * @param integer $content_id  内容id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条内容
     */
    public static function next($content_id, $is_category = 0)
    {
        $where[] = ['content_id', '>', $content_id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $content = self::info($content_id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $content = Db::name(self::$db_name)
            ->field('content_id,name')
            ->where($where)
            ->order('content_id', 'asc')
            ->find();
        if (empty($content)) {
            return [];
        }

        return $content;
    }

    /**
     * 内容回收站恢复
     * 
     * @param array $content 内容列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($content)
    {
        $content_ids = array_column($content, 'content_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 内容回收站删除
     * 
     * @param array $content 内容列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($content)
    {
        $content_ids = array_column($content, 'content_id');

        $res = Db::name(self::$db_name)
            ->where('content_id', 'in', $content_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($content_ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['content_ids'] = $content_ids;

        return $update;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = ContentCache::get($key);
        if (empty($field)) {
            $sql = Db::name(self::$db_name)
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            ContentCache::set($key, $field);
        }

        return $field;
    }

    /**
     * 表字段是否存在
     * 
     * @param string $field 要检查的字段
     * 
     * @return bool
     */
    public static function tableFieldExist($field)
    {
        $fields = self::tableField();

        foreach ($fields as $k => $v) {
            if ($v == $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * 内容统计
     *
     * @return array
     */
    public static function statistics()
    {
        $data['category'] = Db::name('cms_category')->where('is_delete', 0)->count('category_id');
        $data['content']  = Db::name('cms_content')->where('is_delete', 0)->count('content_id');
        $data['hits']     = Db::name('cms_content')->where('is_delete', 0)->sum('hits');

        return $data;
    }
}
