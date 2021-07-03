<?php
/*
 * @Description  : 文章分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-28
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\ArticleCategoryCache;
use app\common\utils\ByteUtils;

class ArticleCategoryService
{
    protected static $allkey = 'all';

    /**
     * 文章分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$allkey;
        $data = ArticleCategoryCache::get($key);
        if (empty($data)) {
            $field = 'article_category_id,article_category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'article_category_id' => 'desc'];

            $list = Db::name('article_category')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            ArticleCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 文章分类信息
     * 
     * @param integer $article_category_id 文章分类id
     * 
     * @return array|Exception
     */
    public static function info($article_category_id)
    {
        $article_category = ArticleCategoryCache::get($article_category_id);
        if (empty($article_category)) {
            $article_category = Db::name('article_category')
                ->where('article_category_id', $article_category_id)
                ->find();
            if (empty($article_category)) {
                exception('文章分类不存在：' . $article_category_id);
            }
            
            $article_category['imgs'] = file_unser($article_category['imgs']);

            ArticleCategoryCache::set($article_category_id, $article_category);
        }

        return $article_category;
    }

    /**
     * 文章分类添加
     *
     * @param array $param 文章分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $article_category_id = Db::name('article_category')
            ->insertGetId($param);
        if (empty($article_category_id)) {
            exception();
        }

        ArticleCategoryCache::del(self::$allkey);

        $param['article_category_id'] = $article_category_id;

        return $param;
    }

    /**
     * 文章分类修改 
     *     
     * @param array $param 文章分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $article_category_id = $param['article_category_id'];

        unset($param['article_category_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $res = Db::name('article_category')
            ->where('article_category_id', $article_category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ArticleCategoryCache::del(self::$allkey);
        ArticleCategoryCache::del($article_category_id);

        $param['article_category_id'] = $article_category_id;

        return $param;
    }

    /**
     * 文章分类删除
     * 
     * @param array $article_category 文章分类
     * 
     * @return array|Exception
     */
    public static function dele($article_category)
    {
        $article_category_ids = array_column($article_category, 'article_category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('article_category')
            ->where('article_category_id', 'in', $article_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_category_ids as $k => $v) {
            ArticleCategoryCache::del($v);
        }
        ArticleCategoryCache::del(self::$allkey);

        $update['article_category_ids'] = $article_category_ids;

        return $update;
    }

    /**
     * 文章分类上传图片
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
            ->putFile('cms/article_category', $file, function () use ($type) {
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
     * 文章分类是否隐藏
     *
     * @param array $param 文章分类
     * 
     * @return array|Exception
     */
    public static function ishide($param)
    {
        $article_category     = $param['article_category'];
        $article_category_ids = array_column($article_category, 'article_category_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('article_category')
            ->where('article_category_id', 'in', $article_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_category_ids as $k => $v) {
            ArticleCategoryCache::del($v);
        }
        ArticleCategoryCache::del(self::$allkey);

        $update['article_category_ids'] = $article_category_ids;

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
        $article_category_id  = isset($data['article_category_id']) ? $data['article_category_id'] : '';
        $article_category_pid = isset($data['article_category_pid']) ? $data['article_category_pid'] : 0;
        $category_name        = $data['category_name'];
        if ($article_category_id) {
            if ($article_category_id == $article_category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = ['article_category_id', '<>', $article_category_id];
        }

        $where[] = ['article_category_pid', '=', $article_category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name('article_category')
            ->field('article_category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    /**
     * 文章分类列表转树形
     *
     * @param array   $article_category     文章分类列表
     * @param integer $article_category_pid 文章分类父级id
     * 
     * @return array
     */
    public static function toTree($article_category, $article_category_pid)
    {
        $tree = [];

        foreach ($article_category as $k => $v) {
            if ($v['article_category_pid'] == $article_category_pid) {
                $v['children'] = self::toTree($article_category, $v['article_category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
