<?php
/*
 * @Description  : 开发文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-18
 * @LastEditTime : 2020-09-18
 */

namespace app\admin\service;

use think\facade\Db;
use app\cache\AdminDevdocCache;

class AdminDevdocService
{
    /**
     * 文档列表
     *
     * @return array 
     */
    public static function list()
    {
        $tree = AdminDevdocCache::get(-1);

        if (empty($tree)) {
            $field = 'admin_devdoc_id,devdoc_pid,devdoc_name,devdoc_sort,create_time,update_time';

            $admin_devdoc_pid = Db::name('admin_devdoc')
                ->field($field)
                ->where('devdoc_pid', '=', 0)
                ->where('is_delete', 0)
                ->order(['admin_devdoc_id' => 'asc', 'devdoc_sort' => 'desc'])
                ->select()
                ->toArray();

            $admin_devdoc_child = Db::name('admin_devdoc')
                ->field($field)
                ->where('devdoc_pid', '>', 0)
                ->where('is_delete', 0)
                ->order(['devdoc_sort' => 'desc', 'admin_devdoc_id' => 'asc',])
                ->select()
                ->toArray();

            $admin_devdoc = array_merge($admin_devdoc_pid, $admin_devdoc_child);

            $tree = self::toTree($admin_devdoc, 0);

            AdminDevdocCache::set(-1, $tree);
        }

        $data['count'] = count($tree);
        $data['list']  = $tree;

        return $data;
    }

    /**
     * 文档信息
     * admin_devdoc_id：0所有文档，-1树形文档
     * 
     * @param integer $admin_devdoc_id 文档id
     * 
     * @return array
     */
    public static function info($admin_devdoc_id)
    {
        $admin_devdoc = AdminDevdocCache::get($admin_devdoc_id);

        if (empty($admin_devdoc)) {
            if ($admin_devdoc_id == 0) {
                $where[] = ['is_delete', '=', 0];
                $order   = ['admin_devdoc_id' => 'asc', 'devdoc_sort' => 'desc'];

                $admin_devdoc = Db::name('admin_devdoc')
                    ->field('devdoc_path')
                    ->where($where)
                    ->order($order)
                    ->select('devdoc_path');
            } elseif ($admin_devdoc_id == -1) {
                $admin_devdoc = self::list();
                $admin_devdoc = $admin_devdoc['list'];
            } else {
                $admin_devdoc = Db::name('admin_devdoc')
                    ->where('admin_devdoc_id', $admin_devdoc_id)
                    ->where('is_delete', 0)
                    ->find();

                if (empty($admin_devdoc)) {
                    error('文档不存在');
                }
            }

            if ($admin_devdoc) {
                AdminDevdocCache::set($admin_devdoc_id, $admin_devdoc);
            }
        }

        return $admin_devdoc;
    }

    /**
     * 文档添加
     *
     * @param array $param 文档信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = date('Y-m-d H:i:s');

        $admin_devdoc_id = Db::name('admin_devdoc')
            ->insertGetId($param);

        if (empty($admin_devdoc_id)) {
            error();
        }

        $param['admin_devdoc_id'] = $admin_devdoc_id;

        AdminDevdocCache::del(0);
        AdminDevdocCache::del(-1);

        return $param;
    }

    /**
     * 文档修改
     *
     * @param array $param 文档信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $admin_devdoc_id = $param['admin_devdoc_id'];

        unset($param['admin_devdoc_id']);

        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('admin_devdoc')
            ->where('admin_devdoc_id', $admin_devdoc_id)
            ->update($param);

        if (empty($update)) {
            error();
        }

        $param['admin_devdoc_id'] = $admin_devdoc_id;

        AdminDevdocCache::del(0);
        AdminDevdocCache::del(-1);

        return $param;
    }

    /**
     * 文档删除
     *
     * @param integer $admin_devdoc_id 文档id
     * 
     * @return array
     */
    public static function dele($admin_devdoc_id)
    {
        $admin_devdoc = Db::name('admin_devdoc')
            ->field('admin_devdoc_id,devdoc_pid')
            ->where('is_delete', 0)
            ->select();

        $admin_devdoc_ids   = self::getChildren($admin_devdoc, $admin_devdoc_id);
        $admin_devdoc_ids[] = (int) $admin_devdoc_id;

        $data['is_delete']   = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');

        $update = Db::name('admin_devdoc')
            ->where('admin_devdoc_id', 'in', $admin_devdoc_ids)
            ->update($data);

        if (empty($update)) {
            error();
        }

        AdminDevdocCache::del(0);
        AdminDevdocCache::del(-1);

        return $admin_devdoc_ids;
    }

    /**
     * 文档所有子级获取
     *
     * @param array   $admin_devdoc    所有文档
     * @param integer $admin_devdoc_id 文档id
     * 
     * @return array
     */
    public static function getChildren($admin_devdoc, $admin_devdoc_id)
    {
        $children = [];

        foreach ($admin_devdoc as $k => $v) {
            if ($v['devdoc_pid'] == $admin_devdoc_id) {
                $children[] = $v['admin_devdoc_id'];
                $children   = array_merge($children, self::getChildren($admin_devdoc, $v['admin_devdoc_id']));
            }
        }

        return $children;
    }

    /**
     * 文档树形获取
     *
     * @param array   $admin_devdoc 所有文档
     * @param integer $devdoc_pid   文档父级id
     * 
     * @return array
     */
    public static function toTree($admin_devdoc, $devdoc_pid)
    {
        $tree = [];

        foreach ($admin_devdoc as $k => $v) {
            if ($v['devdoc_pid'] == $devdoc_pid) {
                $v['children'] = self::toTree($admin_devdoc, $v['admin_devdoc_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 文档模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'devdoc_name')
    {
        $data = Db::name('admin_devdoc')
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 文档精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'devdoc_name')
    {
        $data = Db::name('admin_devdoc')
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $data;
    }
}
