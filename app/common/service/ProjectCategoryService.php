<?php
/*
 * @Description  : 案例分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-28
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\ProjectCategoryCache;
use app\common\utils\ByteUtils;

class ProjectCategoryService
{
    protected static $allkey = 'all';

    /**
     * 案例分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$allkey;
        $data = ProjectCategoryCache::get($key);
        if (empty($data)) {
            $field = 'project_category_id,project_category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'project_category_id' => 'desc'];

            $list = Db::name('project_category')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            ProjectCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 案例分类信息
     * 
     * @param integer $project_category_id 案例分类id
     * 
     * @return array|Exception
     */
    public static function info($project_category_id)
    {
        $project_category = ProjectCategoryCache::get($project_category_id);
        if (empty($project_category)) {
            $project_category = Db::name('project_category')
                ->where('project_category_id', $project_category_id)
                ->find();
            if (empty($project_category)) {
                exception('案例分类不存在：' . $project_category_id);
            }

            $project_category['imgs'] = file_unser($project_category['imgs']);

            ProjectCategoryCache::set($project_category_id, $project_category);
        }

        return $project_category;
    }

    /**
     * 案例分类添加
     *
     * @param array $param 案例分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $project_category_id = Db::name('project_category')
            ->insertGetId($param);
        if (empty($project_category_id)) {
            exception();
        }

        ProjectCategoryCache::del(self::$allkey);

        $param['project_category_id'] = $project_category_id;

        return $param;
    }

    /**
     * 案例分类修改 
     *     
     * @param array $param 案例分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $project_category_id = $param['project_category_id'];

        unset($param['project_category_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $res = Db::name('project_category')
            ->where('project_category_id', $project_category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ProjectCategoryCache::del(self::$allkey);
        ProjectCategoryCache::del($project_category_id);

        $param['project_category_id'] = $project_category_id;

        return $param;
    }

    /**
     * 案例分类删除
     * 
     * @param array $project_category 案例分类
     * 
     * @return array|Exception
     */
    public static function dele($project_category)
    {
        $project_category_ids = array_column($project_category, 'project_category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('project_category')
            ->where('project_category_id', 'in', $project_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_category_ids as $k => $v) {
            ProjectCategoryCache::del($v);
        }
        ProjectCategoryCache::del(self::$allkey);

        $update['project_category_ids'] = $project_category_ids;

        return $update;
    }

    /**
     * 案例分类上传图片
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
            ->putFile('cms/project_category', $file, function () use ($type) {
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
     * 案例分类是否隐藏
     *
     * @param array $param 案例分类
     * 
     * @return array|Exception
     */
    public static function ishide($param)
    {
        $project_category     = $param['project_category'];
        $project_category_ids = array_column($project_category, 'project_category_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('project_category')
            ->where('project_category_id', 'in', $project_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_category_ids as $k => $v) {
            ProjectCategoryCache::del($v);
        }
        ProjectCategoryCache::del(self::$allkey);

        $update['project_category_ids'] = $project_category_ids;

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
        $project_category_id  = isset($data['project_category_id']) ? $data['project_category_id'] : '';
        $project_category_pid = isset($data['project_category_pid']) ? $data['project_category_pid'] : 0;
        $category_name        = $data['category_name'];
        if ($project_category_id) {
            if ($project_category_id == $project_category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = ['project_category_id', '<>', $project_category_id];
        }

        $where[] = ['project_category_pid', '=', $project_category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name('project_category')
            ->field('project_category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    /**
     * 案例分类列表转树形
     *
     * @param array   $project_category     案例分类列表
     * @param integer $project_category_pid 案例分类父级id
     * 
     * @return array
     */
    public static function toTree($project_category, $project_category_pid)
    {
        $tree = [];

        foreach ($project_category as $k => $v) {
            if ($v['project_category_pid'] == $project_category_pid) {
                $v['children'] = self::toTree($project_category, $v['project_category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
