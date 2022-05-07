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
     * @param string $type  tree树形，list列表
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array
     */
    public static function list($type = 'tree', $where = [], $order = [], $field = '')
    {
        if ($type == 'list') {
            $model = new CategoryModel();
            $pk = $model->getPk();

            if (empty($field)) {
                $field = $pk . ',category_pid,category_name,sort,is_hide,create_time,update_time,delete_time';
            }
            if (empty($order)) {
                $order = ['sort' => 'desc', $pk => 'desc'];
            }

            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
        } else {
            if (empty($field)) {
                $field = 'category_id,category_pid,category_name,sort,is_hide,create_time,update_time';
            }

            $key = $type . md5(serialize($where) . $field);
            $data = CategoryCache::get($key);
            if (empty($data)) {
                $model = new CategoryModel();
                $pk = $model->getPk();

                if (empty($order)) {
                    $order = ['sort' => 'desc', $pk => 'desc'];
                }

                $data = $model->field($field)->where($where)->order($order)->select()->toArray();
                $data = list_to_tree($data, $pk, 'category_pid');

                CategoryCache::set($key, $data);
            }
        }

        return $data;
    }

    /**
     * 内容分类信息
     * 
     * @param int  $id   内容分类id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = CategoryCache::get($id);
        if (empty($info)) {
            $model = new CategoryModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('内容分类不存在：' . $id);
                }
                return [];
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

        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        CategoryCache::clear();

        return $param;
    }

    /**
     * 内容分类修改 
     *     
     * @param mixed $ids    内容分类信息id
     * @param array $update 内容分类信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $update = [])
    {
        $model = new CategoryModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        CategoryCache::clear();

        return $update;
    }

    /**
     * 内容分类删除
     * 
     * @param mixed $ids  内容分类id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new CategoryModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        CategoryCache::clear();

        return $update;
    }
}
