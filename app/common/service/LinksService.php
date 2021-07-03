<?php
/*
 * @Description  : 友链管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-02
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\utils\ByteUtils;
use app\common\cache\LinksCache;

class LinksService
{
    protected static $allkey = 'all';

    /**
     * 友链列表
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
            $field = 'links_id,name,url,imgs,sort,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'links_id,name,url,imgs,sort,is_top,is_hot,is_rec,is_hide,create_time,update_time');
        }

        if (empty($order)) {
            $order = ['links_id' => 'desc'];
        }

        $count = Db::name('links')
            ->where($where)
            ->count('links_id');

        $list = Db::name('links')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        foreach ($list as $k => $v) {
            $list[$k]['img_url'] = '';
            $imgs = file_unser($v['imgs']);
            if ($imgs) {
                $list[$k]['img_url'] = $imgs[0]['url'];
            }
            unset($list[$k]['imgs']);
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 友链信息
     * 
     * @param $links_id 友链id
     * 
     * @return array|Exception
     */
    public static function info($links_id)
    {
        if ($links_id == 'all') {
            $key = self::$allkey;
            $links = LinksCache::get($key);
            if (empty($links)) {
                $where[] = ['is_hide', '=', 0];
                $where[] = ['is_delete', '=', 0];
                $order = ['sort' => 'desc'];
                $data = self::list($where, 1, 9999, $order);
                $links = $data['list'];
                LinksCache::set($key, $links);
            }
        } else {
            $links = Db::name('links')
                ->where('links_id', $links_id)
                ->find();
            if (empty($links)) {
                exception('友链不存在：' . $links_id);
            }

            $links['imgs'] = file_unser($links['imgs']);
        }

        return $links;
    }

    /**
     * 友链添加
     *
     * @param $param 友链信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $links_id = Db::name('links')
            ->insertGetId($param);
        if (empty($links_id)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $param['links_id'] = $links_id;

        return $param;
    }

    /**
     * 友链修改 
     *     
     * @param $param 友链信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $links_id = $param['links_id'];

        unset($param['links_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $res = Db::name('links')
            ->where('links_id', $links_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $param['links_id'] = $links_id;

        return $param;
    }

    /**
     * 友链删除
     * 
     * @param array $links 友链列表
     * 
     * @return array|Exception
     */
    public static function dele($links)
    {
        $links_ids = array_column($links, 'links_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $update['links_ids'] = $links_ids;

        return $update;
    }

    /**
     * 友链上传文件
     *
     * @param array $param 文件信息
     * 
     * @return array
     */
    public static function upload($param)
    {
        $type = $param['type'];
        $file = $param['file'];

        $file_name = Filesystem::disk('public')
            ->putFile('cms/links', $file, function () use ($type) {
                return date('Ymd') . '/' . date('YmdHis') . '_' . $type;
            });

        $data['type'] = $type;
        $data['path'] = 'storage/' . $file_name;
        $data['url']  = file_url($data['path']);
        $data['name'] = $file->getOriginalName();
        $data['size'] = ByteUtils::format($file->getSize());

        return $data;
    }

    /**
     * 友链是否置顶
     *
     * @param array $param 友链信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $links     = $param['links'];
        $links_ids = array_column($links, 'links_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $update['links_ids'] = $links_ids;

        return $update;
    }

    /**
     * 友链是否热门
     *
     * @param array $param 友链信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $links     = $param['links'];
        $links_ids = array_column($links, 'links_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $update['links_ids'] = $links_ids;

        return $update;
    }

    /**
     * 友链是否推荐
     *
     * @param array $param 友链信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $links     = $param['links'];
        $links_ids = array_column($links, 'links_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $update['links_ids'] = $links_ids;

        return $update;
    }

    /**
     * 友链是否隐藏
     *
     * @param array $param 友链信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $links     = $param['links'];
        $links_ids = array_column($links, 'links_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $update['links_ids'] = $links_ids;

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
        $field = LinksCache::get($key);
        if (empty($field)) {
            $sql = Db::name('links')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            LinksCache::set($key, $field);
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
     * 友链回收站恢复
     * 
     * @param array $links 友链列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($links)
    {
        $links_ids = array_column($links, 'links_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        LinksCache::del(self::$allkey);

        $update['links_ids'] = $links_ids;

        return $update;
    }

    /**
     * 友链回收站删除
     * 
     * @param array $links 友链列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($links)
    {
        $links_ids = array_column($links, 'links_id');

        $res = Db::name('links')
            ->where('links_id', 'in', $links_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        $update['links_ids'] = $links_ids;

        return $update;
    }
}
