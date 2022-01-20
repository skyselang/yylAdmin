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

use app\common\cache\cms\CategoryCache;
use app\common\model\cms\CategoryModel;
use app\common\service\file\FileService;

class CategoryService
{
    /**
     * 内容分类列表
     * 
     * @param string $type  list列表，tree树形
     * @param array  $where 搜索条件
     * 
     * @return array
     */
    public static function list($type = 'list', $where = [])
    {
        if ($where) {
            $model = new CategoryModel();
            $pk = $model->getPk();

            $field = $pk . ',category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', $pk => 'desc'];

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
        } else {
            $key = $type;
            $data = CategoryCache::get($key);
            if (empty($data)) {
                $model = new CategoryModel();
                $pk = $model->getPk();

                $field = $pk . ',category_pid,category_name,sort,is_hide,create_time,update_time';

                $where[] = ['is_delete', '=', 0];

                $order = ['sort' => 'desc', $pk => 'desc'];

                $data = $model->field($field)->where($where)->order($order)->select()->toArray();

                if ($type == 'tree') {
                    $data = self::toTree($data, 0);
                }

                CategoryCache::set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 内容分类信息
     * 
     * @param int $id 内容分类id
     * 
     * @return array|Exception
     */
    public static function info($id)
    {
        $info = CategoryCache::get($id);
        if (empty($info)) {
            $model = new CategoryModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                exception('内容分类不存在：' . $id);
            }
            $info = $info->toArray();

            $info['imgs'] = FileService::fileArray($info['img_ids']);

            CategoryCache::set($id, $info);
        }

        return $info;
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
        $model = new CategoryModel();
        $pk = $model->getPk();

        $param['img_ids']     = file_ids($param['imgs']);
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        CategoryCache::del();

        $param[$pk] = $id;

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
        $model = new CategoryModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['img_ids']     = file_ids($param['imgs']);
        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        CategoryCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 内容分类删除
     * 
     * @param array $ids 内容分类id
     * 
     * @return array|Exception
     */
    public static function dele($ids)
    {
        $model = new CategoryModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        CategoryCache::del($ids);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容分类修改上级
     *
     * @param array $ids          内容分类id
     * @param int   $category_pid 内容分类pid
     * 
     * @return array
     */
    public static function pid($ids, $category_pid = 0)
    {
        $model = new CategoryModel();
        $pk = $model->getPk();

        $update['category_pid'] = $category_pid;
        $update['update_time']  = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        CategoryCache::del($ids);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容分类是否隐藏
     *
     * @param array $ids     内容分类id
     * @param int   $is_hide 是否隐藏
     * 
     * @return array|Exception
     */
    public static function ishide($ids, $is_hide)
    {
        $model = new CategoryModel();
        $pk = $model->getPk();

        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        CategoryCache::del($ids);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容分类列表转树形
     *
     * @param array $category     内容分类列表
     * @param int   $category_pid 内容分类pid
     * 
     * @return array
     */
    public static function toTree($category, $category_pid)
    {
        $model = new CategoryModel();
        $pk = $model->getPk();

        $tree = [];
        foreach ($category as $v) {
            if ($v['category_pid'] == $category_pid) {
                $v['children'] = self::toTree($category, $v[$pk]);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
