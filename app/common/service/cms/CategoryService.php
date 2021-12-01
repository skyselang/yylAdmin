<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容分类
namespace app\common\service\cms;

use think\facade\Db;
use app\common\cache\cms\CategoryCache;
use app\common\service\file\FileService;

class CategoryService
{
    // 表名
    protected static $t_name = 'cms_category';
    // 表主键
    protected static $t_pk = 'category_id';
    // 缓存key
    protected static $all_key = 'all';

    /**
     * 分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key  = self::$all_key;
        $data = CategoryCache::get($key);
        if (empty($data)) {
            $field = self::$t_pk . ',category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', self::$t_pk => 'desc'];

            $list = Db::name(self::$t_name)
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            CategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 分类信息
     * 
     * @param integer $category_id 分类id
     * 
     * @return array|Exception
     */
    public static function info($category_id)
    {
        $category = CategoryCache::get($category_id);
        if (empty($category)) {
            $category = Db::name(self::$t_name)
                ->where(self::$t_pk, $category_id)
                ->find();
            if (empty($category)) {
                exception('分类不存在：' . $category_id);
            }

            $category['imgs'] = FileService::fileArray($category['img_ids']);

            CategoryCache::set($category_id, $category);
        }

        return $category;
    }

    /**
     * 分类添加
     *
     * @param array $param 分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['img_ids']     = file_ids($param['imgs']);
        $param['create_time'] = datetime();

        $category_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($category_id)) {
            exception();
        }

        CategoryCache::del(self::$all_key);

        $param[self::$t_pk] = $category_id;

        return $param;
    }

    /**
     * 分类修改 
     *     
     * @param array $param 分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $category_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $param['img_ids']     = file_ids($param['imgs']);
        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        CategoryCache::del($category_id);
        CategoryCache::del(self::$all_key);

        $param[self::$t_pk] = $category_id;

        return $param;
    }

    /**
     * 分类删除
     * 
     * @param array $category 分类列表
     * 
     * @return array|Exception
     */
    public static function dele($category)
    {
        $category_ids = array_column($category, self::$t_pk);

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($category_ids as $k => $v) {
            CategoryCache::del($v);
        }
        CategoryCache::del(self::$all_key);

        $update['category_ids'] = $category_ids;

        return $update;
    }

    /**
     * 内容分类设置父级
     *
     * @param array $category     分类列表
     * @param int   $category_pid 分类父级id
     * 
     * @return array
     */
    public static function pid($category, $category_pid = 0)
    {
        $category_ids = array_column($category, self::$t_pk);

        $update['category_pid'] = $category_pid;
        $update['update_time']  = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($category_ids as $k => $v) {
            CategoryCache::del($v);
        }
        CategoryCache::del(self::$all_key);

        $update['category_ids'] = $category_ids;

        return $update;
    }

    /**
     * 分类是否隐藏
     *
     * @param array $category 分类列表
     * @param int   $is_hide  是否隐藏
     * 
     * @return array|Exception
     */
    public static function ishide($category, $is_hide)
    {
        $category_ids = array_column($category, self::$t_pk);

        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($category_ids as $k => $v) {
            CategoryCache::del($v);
        }
        CategoryCache::del(self::$all_key);

        $update['category_ids'] = $category_ids;

        return $update;
    }

    /**
     * 分类列表转树形
     *
     * @param array   $category     分类列表
     * @param integer $category_pid 分类父级id
     * 
     * @return array
     */
    public static function toTree($category, $category_pid)
    {
        $tree = [];

        foreach ($category as $k => $v) {
            if ($v['category_pid'] == $category_pid) {
                $v['children'] = self::toTree($category, $v[self::$t_pk]);
                $tree[] = $v;
            }
        }

        return $tree;
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
        $category_id   = isset($data[self::$t_pk]) ? $data[self::$t_pk] : '';
        $category_pid  = isset($data['category_pid']) ? $data['category_pid'] : 0;
        $category_name = $data['category_name'];
        if ($category_id) {
            if ($category_id == $category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = [self::$t_pk, '<>', $category_id];
        }

        $where[] = ['category_pid', '=', $category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name(self::$t_name)
            ->field(self::$t_pk)
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }
}
