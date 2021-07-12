<?php
/*
 * @Description  : 内容分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-09
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\utils\ByteUtils;
use app\common\cache\CmsCategoryCache;

class CmsCategoryService
{
    // 内容分类表名
    protected static $db_name = 'cms_category';
    // 内容分类缓存key
    protected static $all_key = 'all';

    /**
     * 内容分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$all_key;
        $data = CmsCategoryCache::get($key);
        if (empty($data)) {
            $field = 'category_id,category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'category_id' => 'desc'];

            $list = Db::name(self::$db_name)
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            CmsCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 内容分类信息
     * 
     * @param integer $category_id 内容分类id
     * 
     * @return array|Exception
     */
    public static function info($category_id)
    {
        $category = CmsCategoryCache::get($category_id);
        if (empty($category)) {
            $category = Db::name(self::$db_name)
                ->where('category_id', $category_id)
                ->find();
            if (empty($category)) {
                exception('内容分类不存在：' . $category_id);
            }

            $category['imgs'] = file_unser($category['imgs']);

            CmsCategoryCache::set($category_id, $category);
        }

        return $category;
    }

    /**
     * 内容分类添加
     *
     * @param array $param 内容分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['imgs']        = file_ser($param['imgs']);
        $param['create_time'] = datetime();

        $category_id = Db::name(self::$db_name)
            ->insertGetId($param);
        if (empty($category_id)) {
            exception();
        }

        CmsCategoryCache::del(self::$all_key);

        $param['category_id'] = $category_id;

        return $param;
    }

    /**
     * 内容分类修改 
     *     
     * @param array $param 内容分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $category_id = $param['category_id'];

        unset($param['category_id']);

        $param['imgs']        = file_ser($param['imgs']);
        $param['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('category_id', $category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        CmsCategoryCache::del(self::$all_key);
        CmsCategoryCache::del($category_id);

        $param['category_id'] = $category_id;

        return $param;
    }

    /**
     * 内容分类删除
     * 
     * @param array $category 内容分类
     * 
     * @return array|Exception
     */
    public static function dele($category)
    {
        $category_ids = array_column($category, 'category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('category_id', 'in', $category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($category_ids as $k => $v) {
            CmsCategoryCache::del($v);
        }
        CmsCategoryCache::del(self::$all_key);

        $update['category_ids'] = $category_ids;

        return $update;
    }

    /**
     * 内容分类上传图片
     *
     * @param file   $param 文件
     * @param string $param 类型
     * 
     * @return array
     */
    public static function upload($file, $type)
    {
        $file_name = Filesystem::disk('public')
            ->putFile('cms/category', $file, function () use ($type) {
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
     * 内容分类是否隐藏
     *
     * @param array $cms     内容分类
     * @param int   $is_hide 是否隐藏
     * 
     * @return array|Exception
     */
    public static function ishide($category, $is_hide)
    {
        $category_ids = array_column($category, 'category_id');

        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();

        $res = Db::name(self::$db_name)
            ->where('category_id', 'in', $category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($category_ids as $k => $v) {
            CmsCategoryCache::del($v);
        }
        CmsCategoryCache::del(self::$all_key);

        $update['category_ids'] = $category_ids;

        return $update;
    }

    /**
     * 内容分类列表转树形
     *
     * @param array   $category     内容分类列表
     * @param integer $category_pid 内容分类父级id
     * 
     * @return array
     */
    public static function toTree($category, $category_pid)
    {
        $tree = [];

        foreach ($category as $k => $v) {
            if ($v['category_pid'] == $category_pid) {
                $v['children'] = self::toTree($category, $v['category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 内容分类名称是否已存在
     *
     * @param array $data 内容分类数据
     *
     * @return bool
     */
    public static function checkCategoryName($data)
    {
        $category_id   = isset($data['category_id']) ? $data['category_id'] : '';
        $category_pid  = isset($data['category_pid']) ? $data['category_pid'] : 0;
        $category_name = $data['category_name'];
        if ($category_id) {
            if ($category_id == $category_pid) {
                return '内容分类父级不能等于内容分类本身';
            }
            $where[] = ['category_id', '<>', $category_id];
        }

        $where[] = ['category_pid', '=', $category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name(self::$db_name)
            ->field('category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '内容分类名称已存在：' . $category_name;
        }

        return true;
    }
}
