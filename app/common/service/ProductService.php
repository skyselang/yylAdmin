<?php
/*
 * @Description  : 产品管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\ProductCache;
use app\common\utils\ByteUtils;

class ProductService
{
    /**
     * 产品列表
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
            $field = 'product_id,product_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'product_id,product_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['product_id' => 'desc'];
        }

        $count = Db::name('product')
            ->where($where)
            ->count('product_id');

        $list = Db::name('product')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $product_category = ProductCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($product_category as $kp => $vp) {
                if ($v['product_category_id'] == $vp['product_category_id']) {
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
     * 产品信息
     * 
     * @param integer $product_id 产品id
     * 
     * @return array|Exception
     */
    public static function info($product_id)
    {
        $product = ProductCache::get($product_id);

        if (empty($product)) {
            $product = Db::name('product')
                ->where('product_id', $product_id)
                ->find();
            if (empty($product)) {
                exception('产品不存在：' . $product_id);
            }

            $product_category = ProductCategoryService::info($product['product_category_id']);

            $product['category_name'] = $product_category['category_name'];
            $product['imgs']          = file_unser($product['imgs']);
            $product['files']         = file_unser($product['files']);

            ProductCache::set($product_id, $product);
        }

        // 点击量
        $gate = 10;
        $key = $product_id . 'Hits';
        $hits = ProductCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name('product')
                    ->where('product_id', '=', $product_id)
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    ProductCache::del($key);
                }
            } else {
                ProductCache::inc($key, 1);
            }
        } else {
            ProductCache::set($key, 1);
        }

        return $product;
    }

    /**
     * 产品添加
     *
     * @param array $param 产品信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $product_id = Db::name('product')
            ->insertGetId($param);
        if (empty($product_id)) {
            exception();
        }

        $param['product_id'] = $product_id;
        $param['imgs']       = file_unser($param['imgs']);
        $param['files']      = file_unser($param['files']);

        return $param;
    }

    /**
     * 产品修改 
     *     
     * @param array $param 产品信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $product_id = $param['product_id'];

        unset($param['product_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $res = Db::name('product')
            ->where('product_id', $product_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ProductCache::del($product_id);

        $param['product_id'] = $product_id;

        return $param;
    }

    /**
     * 产品删除
     * 
     * @param array $product 产品列表
     * 
     * @return array|Exception
     */
    public static function dele($product)
    {
        $product_ids = array_column($product, 'product_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }

    /**
     * 产品上传文件
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
            ->putFile('cms/product', $file, function () use ($type) {
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
     * 产品是否置顶
     *
     * @param array $param 产品信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $product     = $param['product'];
        $product_ids = array_column($product, 'product_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }

    /**
     * 产品是否热门
     *
     * @param array $param 产品信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $product     = $param['product'];
        $product_ids = array_column($product, 'product_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }

    /**
     * 产品是否推荐
     *
     * @param array $param 产品信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $product     = $param['product'];
        $product_ids = array_column($product, 'product_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }

    /**
     * 产品是否隐藏
     *
     * @param array $param 产品信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $product     = $param['product'];
        $product_ids = array_column($product, 'product_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }

    /**
     * 产品上一条
     *
     * @param integer $product_id  产品id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条产品
     */
    public static function prev($product_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['product_id', '<', $product_id];
        if ($is_category) {
            $product = self::info($product_id);
            $where[] = ['product_category_id', '=', $product['product_category_id']];
        }

        $product = Db::name('product')
            ->field('product_id,name')
            ->where($where)
            ->order('product_id', 'desc')
            ->find();
        if (empty($product)) {
            return [];
        }

        return $product;
    }

    /**
     * 产品下一条
     *
     * @param integer $product_id  产品id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条产品
     */
    public static function next($product_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['product_id', '>', $product_id];
        if ($is_category) {
            $product = self::info($product_id);
            $where[] = ['product_category_id', '=', $product['product_category_id']];
        }

        $product = Db::name('product')
            ->field('product_id,name')
            ->where($where)
            ->order('product_id', 'asc')
            ->find();
        if (empty($product)) {
            return [];
        }

        return $product;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = ProductCache::get($key);
        if (empty($field)) {
            $sql = Db::name('product')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            ProductCache::set($key, $field);
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
     * 产品回收站恢复
     * 
     * @param array $product 产品列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($product)
    {
        $product_ids = array_column($product, 'product_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }

    /**
     * 产品回收站删除
     * 
     * @param array $product 产品列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($product)
    {
        $product_ids = array_column($product, 'product_id');

        $res = Db::name('product')
            ->where('product_id', 'in', $product_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($product_ids as $k => $v) {
            ProductCache::del($v);
        }

        $update['product_ids'] = $product_ids;

        return $update;
    }
}
