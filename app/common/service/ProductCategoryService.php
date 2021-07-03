<?php
/*
 * @Description  : 产品分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-28
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\ProductCategoryCache;
use app\common\utils\ByteUtils;

class ProductCategoryService
{
    protected static $allkey = 'all';

    /**
     * 产品分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$allkey;
        $data = ProductCategoryCache::get($key);
        if (empty($data)) {
            $field = 'product_category_id,product_category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'product_category_id' => 'desc'];

            $list = Db::name('product_category')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            ProductCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 产品分类信息
     * 
     * @param $product_category_id 产品分类id
     * 
     * @return array|Exception
     */
    public static function info($product_category_id)
    {
        $product_category = ProductCategoryCache::get($product_category_id);
        if (empty($product_category)) {
            $product_category = Db::name('product_category')
                ->where('product_category_id', $product_category_id)
                ->find();
            if (empty($product_category)) {
                exception('产品分类不存在：' . $product_category_id);
            }

            $product_category['imgs'] = file_unser($product_category['imgs']);

            ProductCategoryCache::set($product_category_id, $product_category);
        }

        return $product_category;
    }

    /**
     * 产品分类添加
     *
     * @param $param 产品分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $product_category_id = Db::name('product_category')
            ->insertGetId($param);
        if (empty($product_category_id)) {
            exception();
        }

        ProductCategoryCache::del(self::$allkey);

        $param['product_category_id'] = $product_category_id;

        return $param;
    }

    /**
     * 产品分类修改 
     *     
     * @param $param 产品分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $product_category_id = $param['product_category_id'];

        unset($param['product_category_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $res = Db::name('product_category')
            ->where('product_category_id', $product_category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ProductCategoryCache::del(self::$allkey);
        ProductCategoryCache::del($product_category_id);

        $param['product_category_id'] = $product_category_id;

        return $param;
    }

    /**
     * 产品分类删除
     * 
     * @param array $product_category 产品分类
     * 
     * @return array|Exception
     */
    public static function dele($product_category)
    {
        $product_category_ids = array_column($product_category, 'product_category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('product_category')
            ->where('product_category_id', 'in', $product_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_category_ids as $k => $v) {
            ProductCategoryCache::del($v);
        }
        ProductCategoryCache::del(self::$allkey);

        $update['product_category_ids'] = $product_category_ids;

        return $update;
    }

    /**
     * 产品分类上传图片
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
            ->putFile('cms/product_category', $file, function () use ($type) {
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
     * 产品分类是否隐藏
     *
     * @param array $param 产品分类
     * 
     * @return array|Exception
     */
    public static function ishide($param)
    {
        $product_category     = $param['product_category'];
        $product_category_ids = array_column($product_category, 'product_category_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('product_category')
            ->where('product_category_id', 'in', $product_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($product_category_ids as $k => $v) {
            ProductCategoryCache::del($v);
        }
        ProductCategoryCache::del(self::$allkey);

        $update['product_category_ids'] = $product_category_ids;

        return $update;
    }

    /**
     * 分类名称是否已存在
     *
     * @param array $data 分类数据
     *
     * @return bool
     */
    public static function checkCategoryName($data)
    {
        $product_category_id  = isset($data['product_category_id']) ? $data['product_category_id'] : '';
        $product_category_pid = isset($data['product_category_pid']) ? $data['product_category_pid'] : 0;
        $category_name        = $data['category_name'];
        if ($product_category_id) {
            if ($product_category_id == $product_category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = ['product_category_id', '<>', $product_category_id];
        }

        $where[] = ['product_category_pid', '=', $product_category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name('product_category')
            ->field('product_category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    /**
     * 产品分类列表转树形
     *
     * @param array   $product_category     产品分类列表
     * @param integer $product_category_pid 产品分类父级id
     * 
     * @return array
     */
    public static function toTree($product_category, $product_category_pid)
    {
        $tree = [];

        foreach ($product_category as $k => $v) {
            if ($v['product_category_pid'] == $product_category_pid) {
                $v['children'] = self::toTree($product_category, $v['product_category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
