<?php
/*
 * @Description  : 接口文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-17
 * @LastEditTime : 2020-09-18
 */

namespace app\admin\service;

use think\facade\Db;
use app\cache\AdminApidocCache;

class AdminApidocService
{
    /**
     * 文档列表
     *
     * @return array 
     */
    public static function list()
    {
        $tree = AdminApidocCache::get(-1);

        if (empty($tree)) {
            $field = 'admin_apidoc_id,apidoc_pid,apidoc_name,apidoc_method,apidoc_path,apidoc_sort,create_time,update_time';

            $admin_apidoc_pid = Db::name('admin_apidoc')
                ->field($field)
                ->where('apidoc_pid', '=', 0)
                ->where('is_delete', 0)
                ->order(['admin_apidoc_id' => 'asc', 'apidoc_sort' => 'desc'])
                ->select()
                ->toArray();

            $admin_apidoc_child = Db::name('admin_apidoc')
                ->field($field)
                ->where('apidoc_pid', '>', 0)
                ->where('is_delete', 0)
                ->order(['apidoc_sort' => 'desc', 'admin_apidoc_id' => 'asc',])
                ->select()
                ->toArray();

            $admin_apidoc = array_merge($admin_apidoc_pid, $admin_apidoc_child);

            $tree = self::toTree($admin_apidoc, 0);

            AdminApidocCache::set(-1, $tree);
        }

        $data['count'] = count($tree);
        $data['list']  = $tree;

        return $data;
    }

    /**
     * 文档信息
     * admin_apidoc_id：0所有文档，-1树形文档
     * 
     * @param integer $admin_apidoc_id 文档id
     * 
     * @return array
     */
    public static function info($admin_apidoc_id)
    {
        $admin_apidoc = AdminApidocCache::get($admin_apidoc_id);

        if (empty($admin_apidoc)) {
            if ($admin_apidoc_id == 0) {
                $where[] = ['is_delete', '=', 0];
                $order   = ['admin_apidoc_id' => 'asc', 'apidoc_sort' => 'desc'];

                $admin_apidoc = Db::name('admin_apidoc')
                    ->field('apidoc_path')
                    ->where($where)
                    ->order($order)
                    ->select('apidoc_path');
            } elseif ($admin_apidoc_id == -1) {
                $admin_apidoc = self::list();
                $admin_apidoc = $admin_apidoc['list'];
            } else {
                $admin_apidoc = Db::name('admin_apidoc')
                    ->where('admin_apidoc_id', $admin_apidoc_id)
                    ->where('is_delete', 0)
                    ->find();

                if (empty($admin_apidoc)) {
                    error('文档不存在');
                }
            }

            if ($admin_apidoc) {
                AdminApidocCache::set($admin_apidoc_id, $admin_apidoc);
            }
        }

        return $admin_apidoc;
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

        $admin_apidoc_id = Db::name('admin_apidoc')
            ->insertGetId($param);

        if (empty($admin_apidoc_id)) {
            error();
        }

        $param['admin_apidoc_id'] = $admin_apidoc_id;

        AdminApidocCache::del(0);
        AdminApidocCache::del(-1);

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
        $admin_apidoc_id = $param['admin_apidoc_id'];

        unset($param['admin_apidoc_id']);

        $param['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('admin_apidoc')
            ->where('admin_apidoc_id', $admin_apidoc_id)
            ->update($param);

        if (empty($update)) {
            error();
        }

        $param['admin_apidoc_id'] = $admin_apidoc_id;

        AdminApidocCache::del(0);
        AdminApidocCache::del(-1);

        return $param;
    }

    /**
     * 文档删除
     *
     * @param integer $admin_apidoc_id 文档id
     * 
     * @return array
     */
    public static function dele($admin_apidoc_id)
    {
        $admin_apidoc = Db::name('admin_apidoc')
            ->field('admin_apidoc_id,apidoc_pid')
            ->where('is_delete', 0)
            ->select();

        $admin_apidoc_ids   = self::getChildren($admin_apidoc, $admin_apidoc_id);
        $admin_apidoc_ids[] = (int) $admin_apidoc_id;

        $data['is_delete']   = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');

        $update = Db::name('admin_apidoc')
            ->where('admin_apidoc_id', 'in', $admin_apidoc_ids)
            ->update($data);

        if (empty($update)) {
            error();
        }

        AdminApidocCache::del(0);
        AdminApidocCache::del(-1);

        return $admin_apidoc_ids;
    }

    /**
     * 文档所有子级获取
     *
     * @param array   $admin_apidoc    所有文档
     * @param integer $admin_apidoc_id 文档id
     * 
     * @return array
     */
    public static function getChildren($admin_apidoc, $admin_apidoc_id)
    {
        $children = [];

        foreach ($admin_apidoc as $k => $v) {
            if ($v['apidoc_pid'] == $admin_apidoc_id) {
                $children[] = $v['admin_apidoc_id'];
                $children   = array_merge($children, self::getChildren($admin_apidoc, $v['admin_apidoc_id']));
            }
        }

        return $children;
    }

    /**
     * 文档树形获取
     *
     * @param array   $admin_apidoc 所有文档
     * @param integer $apidoc_pid   文档父级id
     * 
     * @return array
     */
    public static function toTree($admin_apidoc, $apidoc_pid)
    {
        $tree = [];

        foreach ($admin_apidoc as $k => $v) {
            if ($v['apidoc_pid'] == $apidoc_pid) {
                $v['children'] = self::toTree($admin_apidoc, $v['admin_apidoc_id']);
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
    public static function likeQuery($keyword, $field = 'apidoc_name|apidoc_path')
    {
        $data = Db::name('admin_apidoc')
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
    public static function etQuery($keyword, $field = 'apidoc_name|apidoc_path')
    {
        $data = Db::name('admin_apidoc')
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $data;
    }
}
