<?php
/*
 * @Description  : 轮播管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-02
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\utils\ByteUtils;
use app\common\cache\CarouselCache;

class CarouselService
{
    protected static $allkey = 'all';

    /**
     * 轮播列表
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
            $field = 'carousel_id,name,url,imgs,sort,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'carousel_id,name,url,imgs,sort,is_top,is_hot,is_rec,is_hide,create_time,update_time');
        }

        if (empty($order)) {
            $order = ['carousel_id' => 'desc'];
        }

        $count = Db::name('carousel')
            ->where($where)
            ->count('carousel_id');

        $list = Db::name('carousel')
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
     * 轮播信息
     * 
     * @param $carousel_id 轮播id
     * 
     * @return array|Exception
     */
    public static function info($carousel_id)
    {
        if ($carousel_id == 'all') {
            $key = self::$allkey;
            $carousel = CarouselCache::get($key);
            if (empty($carousel)) {
                $where[] = ['is_hide', '=', 0];
                $where[] = ['is_delete', '=', 0];
                $order = ['sort' => 'desc'];
                $data = self::list($where, 1, 9999, $order);
                $carousel = $data['list'];
                CarouselCache::set($key, $carousel);
            }
        } else {
            $carousel = Db::name('carousel')
                ->where('carousel_id', $carousel_id)
                ->find();
            if (empty($carousel)) {
                exception('轮播不存在：' . $carousel_id);
            }

            $carousel['imgs'] = file_unser($carousel['imgs']);
        }

        return $carousel;
    }

    /**
     * 轮播添加
     *
     * @param $param 轮播信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $carousel_id = Db::name('carousel')
            ->insertGetId($param);
        if (empty($carousel_id)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $param['carousel_id'] = $carousel_id;

        return $param;
    }

    /**
     * 轮播修改 
     *     
     * @param $param 轮播信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $carousel_id = $param['carousel_id'];

        unset($param['carousel_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $res = Db::name('carousel')
            ->where('carousel_id', $carousel_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $param['carousel_id'] = $carousel_id;

        return $param;
    }

    /**
     * 轮播删除
     * 
     * @param array $carousel 轮播列表
     * 
     * @return array|Exception
     */
    public static function dele($carousel)
    {
        $carousel_ids = array_column($carousel, 'carousel_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $update['carousel_ids'] = $carousel_ids;

        return $update;
    }

    /**
     * 轮播上传文件
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
            ->putFile('cms/carousel', $file, function () use ($type) {
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
     * 轮播是否置顶
     *
     * @param array $param 轮播信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $carousel     = $param['carousel'];
        $carousel_ids = array_column($carousel, 'carousel_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $update['carousel_ids'] = $carousel_ids;

        return $update;
    }

    /**
     * 轮播是否热门
     *
     * @param array $param 轮播信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $carousel     = $param['carousel'];
        $carousel_ids = array_column($carousel, 'carousel_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $update['carousel_ids'] = $carousel_ids;

        return $update;
    }

    /**
     * 轮播是否推荐
     *
     * @param array $param 轮播信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $carousel     = $param['carousel'];
        $carousel_ids = array_column($carousel, 'carousel_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $update['carousel_ids'] = $carousel_ids;

        return $update;
    }

    /**
     * 轮播是否隐藏
     *
     * @param array $param 轮播信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $carousel     = $param['carousel'];
        $carousel_ids = array_column($carousel, 'carousel_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $update['carousel_ids'] = $carousel_ids;

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
        $field = CarouselCache::get($key);
        if (empty($field)) {
            $sql = Db::name('carousel')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            CarouselCache::set($key, $field);
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
     * 轮播回收站恢复
     * 
     * @param array $carousel 轮播列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($carousel)
    {
        $carousel_ids = array_column($carousel, 'carousel_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        CarouselCache::del(self::$allkey);

        $update['carousel_ids'] = $carousel_ids;

        return $update;
    }

    /**
     * 轮播回收站删除
     * 
     * @param array $carousel 轮播列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($carousel)
    {
        $carousel_ids = array_column($carousel, 'carousel_id');

        $res = Db::name('carousel')
            ->where('carousel_id', 'in', $carousel_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        $update['carousel_ids'] = $carousel_ids;

        return $update;
    }
}
