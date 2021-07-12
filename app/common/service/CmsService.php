<?php
/*
 * @Description  : 内容管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-09
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\utils\ByteUtils;
use app\common\cache\CmsCache;

class CmsService
{
    // 内容分类表名
    protected static $db_name = 'cms';

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
            $field = 'cms_id,category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'cms_id,category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'cms_id' => 'desc'];
        }

        $count = Db::name(self::$db_name)
            ->where($where)
            ->count('cms_id');

        $list = Db::name(self::$db_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $category = CmsCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($category as $kp => $vp) {
                if ($v['category_id'] == $vp['category_id']) {
                    $list[$k]['category_name'] = $vp['category_name'];
                }
            }

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
     * 内容信息
     * 
     * @param $cms_id 内容id
     * 
     * @return array|Exception
     */
    public static function info($cms_id)
    {
        $cms = CmsCache::get($cms_id);

        if (empty($cms)) {
            $where[] = ['cms_id', '=', $cms_id];
            $cms = Db::name(self::$db_name)
                ->where($where)
                ->find();
            if (empty($cms)) {
                exception('内容不存在：' . $cms_id);
            }

            $category = CmsCategoryService::info($cms['category_id']);

            $cms['category_name'] = $category['category_name'];
            $cms['imgs']          = file_unser($cms['imgs']);
            $cms['files']         = file_unser($cms['files']);
            $cms['videos']        = file_unser($cms['videos']);

            CmsCache::set($cms_id, $cms);
        }

        // 点击量
        $gate = 10;
        $key = $cms['cms_id'] . 'Hits';
        $hits = CmsCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name(self::$db_name)
                    ->where('cms_id', '=', $cms['cms_id'])
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    CmsCache::del($key);
                }
            } else {
                CmsCache::inc($key, 1);
            }
        } else {
            CmsCache::set($key, 1);
        }

        return $cms;
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

        $cms_id = Db::name(self::$db_name)
            ->insertGetId($param);
        if (empty($cms_id)) {
            exception();
        }

        $param['cms_id'] = $cms_id;
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
        $cms_id = $param['cms_id'];

        unset($param['cms_id']);

        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);
        $param['videos']      = file_ser($param['videos']);
        $param['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', $cms_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        CmsCache::del($cms_id);

        $param['cms_id'] = $cms_id;

        return $param;
    }

    /**
     * 内容删除
     * 
     * @param array $cms 内容列表
     * 
     * @return array|Exception
     */
    public static function dele($cms)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

        return $update;
    }

    /**
     * 内容上传文件
     *
     * @param file   $param 文件
     * @param string $param 类型
     * 
     * @return array
     */
    public static function upload($file, $type)
    {
        $file_name = Filesystem::disk('public')
            ->putFile('cms/cms', $file, function () use ($type) {
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
     * 内容是否置顶
     *
     * @param array $cms    内容信息
     * @param int   $is_top 是否置顶
     * 
     * @return array
     */
    public static function istop($cms, $is_top = 0)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $update['is_top']      = $is_top;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

        return $update;
    }

    /**
     * 内容是否热门
     *
     * @param array $cms    内容信息
     * @param int   $is_hot 是否热门
     * 
     * @return array
     */
    public static function ishot($cms, $is_hot = 0)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $update['is_hot']      = $is_hot;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

        return $update;
    }

    /**
     * 内容是否推荐
     *
     * @param array $cms    内容信息
     * @param int   $is_rec 是否推荐
     * 
     * @return array
     */
    public static function isrec($cms, $is_rec = 0)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $update['is_rec']      = $is_rec;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

        return $update;
    }

    /**
     * 内容是否隐藏
     *
     * @param array $cms    内容信息
     * @param int   $is_hide 是否隐藏
     * 
     * @return array
     */
    public static function ishide($cms, $is_hide = 0)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

        return $update;
    }

    /**
     * 内容上一条
     *
     * @param integer $cms_id      内容id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条内容
     */
    public static function prev($cms_id, $is_category = 0)
    {
        $where[] = ['cms_id', '<', $cms_id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $cms = self::info($cms_id);
            $where[] = ['category_id', '=', $cms['category_id']];
        }

        $cms = Db::name(self::$db_name)
            ->field('cms_id,name')
            ->where($where)
            ->order('cms_id', 'desc')
            ->find();
        if (empty($cms)) {
            return [];
        }

        return $cms;
    }

    /**
     * 内容下一条
     *
     * @param integer $cms_id      内容id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条内容
     */
    public static function next($cms_id, $is_category = 0)
    {
        $where[] = ['cms_id', '>', $cms_id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $cms = self::info($cms_id);
            $where[] = ['category_id', '=', $cms['category_id']];
        }

        $cms = Db::name(self::$db_name)
            ->field('cms_id,name')
            ->where($where)
            ->order('cms_id', 'asc')
            ->find();
        if (empty($cms)) {
            return [];
        }

        return $cms;
    }

    /**
     * 内容回收站恢复
     * 
     * @param array $cms 内容列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($cms)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

        return $update;
    }

    /**
     * 内容回收站删除
     * 
     * @param array $cms 内容列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($cms)
    {
        $cms_ids = array_column($cms, 'cms_id');

        $res = Db::name(self::$db_name)
            ->where('cms_id', 'in', $cms_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($cms_ids as $k => $v) {
            CmsCache::del($v);
        }

        $update['cms_ids'] = $cms_ids;

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
        $field = CmsCache::get($key);
        if (empty($field)) {
            $sql = Db::name(self::$db_name)
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            CmsCache::set($key, $field);
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
}
