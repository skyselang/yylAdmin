<?php
/*
 * @Description  : 新闻分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 
 * @LastEditTime : 2021-06-28
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\NewsCategoryCache;
use app\common\utils\ByteUtils;

class NewsCategoryService
{
    protected static $allkey = 'all';

    /**
     * 新闻分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$allkey;
        $data = NewsCategoryCache::get($key);
        if (empty($data)) {
            $field = 'news_category_id,news_category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'news_category_id' => 'desc'];

            $list = Db::name('news_category')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            NewsCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 新闻分类信息
     * 
     * @param integer $news_category_id 新闻分类id
     * 
     * @return array|Exception
     */
    public static function info($news_category_id)
    {
        $news_category = NewsCategoryCache::get($news_category_id);
        if (empty($news_category)) {
            $news_category = Db::name('news_category')
                ->where('news_category_id', $news_category_id)
                ->find();
            if (empty($news_category)) {
                exception('新闻分类不存在：' . $news_category_id);
            }

            $news_category['imgs'] = file_unser($news_category['imgs']);

            NewsCategoryCache::set($news_category_id, $news_category);
        }

        return $news_category;
    }

    /**
     * 新闻分类添加
     *
     * @param array $param 新闻分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $news_category_id = Db::name('news_category')
            ->insertGetId($param);
        if (empty($news_category_id)) {
            exception();
        }

        NewsCategoryCache::del(self::$allkey);

        $param['news_category_id'] = $news_category_id;

        return $param;
    }

    /**
     * 新闻分类修改 
     *     
     * @param array $param 新闻分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $news_category_id = $param['news_category_id'];

        unset($param['news_category_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $res = Db::name('news_category')
            ->where('news_category_id', $news_category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        NewsCategoryCache::del(self::$allkey);
        NewsCategoryCache::del($news_category_id);

        $param['news_category_id'] = $news_category_id;

        return $param;
    }

    /**
     * 新闻分类删除
     * 
     * @param array $news_category 新闻分类
     * 
     * @return array|Exception
     */
    public static function dele($news_category)
    {
        $news_category_ids = array_column($news_category, 'news_category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('news_category')
            ->where('news_category_id', 'in', $news_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_category_ids as $k => $v) {
            NewsCategoryCache::del($v);
        }
        NewsCategoryCache::del(self::$allkey);

        $update['news_category_ids'] = $news_category_ids;

        return $update;
    }

    /**
     * 新闻分类上传图片
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
            ->putFile('cms/news_category', $file, function () use ($type) {
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
     * 新闻分类是否隐藏
     *
     * @param array $param 新闻分类
     * 
     * @return array|Exception
     */
    public static function ishide($param)
    {
        $news_category     = $param['news_category'];
        $news_category_ids = array_column($news_category, 'news_category_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('news_category')
            ->where('news_category_id', 'in', $news_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_category_ids as $k => $v) {
            NewsCategoryCache::del($v);
        }
        NewsCategoryCache::del(self::$allkey);

        $update['news_category_ids'] = $news_category_ids;

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
        $news_category_id  = isset($data['news_category_id']) ? $data['news_category_id'] : '';
        $news_category_pid = isset($data['news_category_pid']) ? $data['news_category_pid'] : 0;
        $category_name     = $data['category_name'];
        if ($news_category_id) {
            if ($news_category_id == $news_category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = ['news_category_id', '<>', $news_category_id];
        }

        $where[] = ['news_category_pid', '=', $news_category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name('news_category')
            ->field('news_category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    /**
     * 新闻分类列表转树形
     *
     * @param array   $news_category     新闻分类列表
     * @param integer $news_category_pid 新闻分类父级id
     * 
     * @return array
     */
    public static function toTree($news_category, $news_category_pid)
    {
        $tree = [];

        foreach ($news_category as $k => $v) {
            if ($v['news_category_pid'] == $news_category_pid) {
                $v['children'] = self::toTree($news_category, $v['news_category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
