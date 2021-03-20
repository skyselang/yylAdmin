<?php
/*
 * @Description  : 地区管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-08
 * @LastEditTime : 2021-03-20
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\RegionCache;
use Overtrue\Pinyin\Pinyin;

class RegionService
{
    // 地区树形key
    protected static $tree_key = 'tree';

    /**
     * 地区列表
     * 
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     *
     * @return array 
     */
    public static function list($where = [], $order = [], $field = '')
    {

        if (empty($field)) {
            $field = 'region_id,region_pid,region_path,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_sort';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['region_sort' => 'desc', 'region_id' => 'asc'];
        }

        $list = Db::name('region')
            ->field($field)
            ->where($where)
            ->order($order)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            $v['children']    = [];
            $v['hasChildren'] = true;
            $list[$k] = $v;
        }

        $data['count'] = count($list);
        $data['list']  = $list;

        return $data;
    }

    /**
     * 地区信息
     * region_id=tree：树形
     *
     * @param integer|string $region_id 地区id
     * 
     * @return array
     */
    public static function info($region_id)
    {
        $region = RegionCache::get($region_id);

        if (empty($region)) {
            if ($region_id == self::$tree_key) {
                $region = Db::name('region')
                    ->field('region_id,region_pid,region_name')
                    ->where('is_delete', '=', 0)
                    ->select()
                    ->toArray();

                $region = self::toTree($region, 0);
            } else {
                $region = Db::name('region')
                    ->where('region_id', $region_id)
                    ->find();

                if (empty($region)) {
                    exception('地区不存在：' . $region_id);
                }

                // 地区完整名称
                $region_path = explode(',', $region['region_path']);
                if (count($region_path) == 1) {
                    $region_fullname    = $region['region_name'];
                    $region_fullname_py = $region['region_pinyin'];
                } else {
                    $region_pid = [];
                    foreach ($region_path as $k => $v) {
                        $region_pid[] = Db::name('region')
                            ->field('region_name,region_pinyin')
                            ->where('region_id', '=', $v)
                            ->find();
                    }
                    $region_fullname    = array_column($region_pid, 'region_name');
                    $region_fullname    = implode('-', $region_fullname);
                    $region_fullname_py = array_column($region_pid, 'region_pinyin');
                    $region_fullname_py = implode('-', $region_fullname_py);
                }
                $region['region_fullname']    = $region_fullname;
                $region['region_fullname_py'] = $region_fullname_py;
            }

            RegionCache::set($region_id, $region);
        }

        return $region;
    }


    /**
     * 地区添加
     *
     * @param array $param 地区信息
     * 
     * @return array
     */
    public static function add($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $region['region_tree'] = self::info('tree');

            return $region;
        } else {
            $param['create_time'] = datetime();

            $Pinyin          = new Pinyin();
            $region_py       = $Pinyin->convert($param['region_name']);
            $region_pinyin   = '';
            $region_jianpin  = '';
            $region_initials = '';

            foreach ($region_py as $k => $v) {
                $region_py_i = '';
                $region_py_e = '';
                $region_py_i = strtoupper(substr($v, 0, 1));
                $region_py_e = substr($v, 1);
                $region_pinyin  .= $region_py_i . $region_py_e;
                $region_jianpin .= $region_py_i;
                if ($k == 0) {
                    $region_initials = $region_py_i;
                }
            }

            $param['region_pinyin']   = $param['region_pinyin'] ?: $region_pinyin;
            $param['region_jianpin']  = $param['region_jianpin'] ?: $region_jianpin;
            $param['region_initials'] = $param['region_initials'] ?: $region_initials;

            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);

                $param['region_level'] = $region['region_level'] + 1;
                $region_id = Db::name('region')
                    ->insertGetId($param);

                $region_path = $region['region_path'] . ',' . $region_id;
                $update['region_path'] = $region_path;
                $update_res = Db::name('region')
                    ->where('region_id', $region_id)
                    ->update($update);
            } else {
                $region_id = Db::name('region')
                    ->insertGetId($param);

                $region_path = $region_id;
                $update['region_path'] = $region_path;
                $update_res = Db::name('region')
                    ->where('region_id', $region_id)
                    ->update($update);
            }

            if (empty($update_res)) {
                exception();
            }

            RegionCache::del(self::$tree_key);

            $param['region_id']   = $region_id;
            $param['region_path'] = $region_path;

            return $param;
        }
    }

    /**
     * 地区修改
     *
     * @param array $param 地区信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $region_id = $param['region_id'];

        if ($method == 'get') {
            $data['region_info'] = self::info($region_id);
            $data['region_tree'] = self::info('tree');

            return $data;
        } else {
            unset($param['region_id']);

            $Pinyin          = new Pinyin();
            $region_py       = $Pinyin->convert($param['region_name']);
            $region_pinyin   = '';
            $region_jianpin  = '';
            $region_initials = '';

            foreach ($region_py as $k => $v) {
                $region_py_i = '';
                $region_py_e = '';
                $region_py_i = strtoupper(substr($v, 0, 1));
                $region_py_e = substr($v, 1);
                $region_pinyin  .= $region_py_i . $region_py_e;
                $region_jianpin .= $region_py_i;
                if ($k == 0) {
                    $region_initials = $region_py_i;
                }
            }

            $param['region_pinyin']   = $param['region_pinyin'] ?: $region_pinyin;
            $param['region_jianpin']  = $param['region_jianpin'] ?: $region_jianpin;
            $param['region_initials'] = $param['region_initials'] ?: $region_initials;

            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);

                $param['region_level'] = $region['region_level'] + 1;
                Db::name('region')
                    ->where('region_id', $region_id)
                    ->update($param);

                $region_path = $region['region_path'] . ',' . $region_id;
                $update['region_path'] = $region_path;
                $update['update_time'] = datetime();
                $update_res = Db::name('region')
                    ->where('region_id', $region_id)
                    ->update($update);
            } else {
                Db::name('region')
                    ->where('region_id', $region_id)
                    ->update($param);

                $region_path = $region_id;
                $update['region_path'] = $region_path;
                $update['update_time'] = datetime();
                $update_res = Db::name('region')
                    ->where('region_id', $region_id)
                    ->update($update);
            }

            if (empty($update_res)) {
                exception();
            }

            $param['region_id']   = $region_id;
            $param['region_path'] = $region_path;

            RegionCache::del(self::$tree_key);
            RegionCache::del($region_id);

            return $param;
        }
    }

    /**
     * 地区删除
     *
     * @param integer $region_id 地区id
     * 
     * @return array
     */
    public static function dele($region_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('region')
            ->where('region_id', '=', $region_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['region_id'] = $region_id;

        RegionCache::del(self::$tree_key);
        RegionCache::del($region_id);

        return $update;
    }

    /**
     * 地区所有子级获取
     *
     * @param array   $region    所有地区
     * @param integer $region_id 地区id
     * 
     * @return array
     */
    public static function getChildren($region, $region_id)
    {
        $children = [];

        foreach ($region as $k => $v) {
            if ($v['region_pid'] == $region_id) {
                $children[] = $v['region_id'];
                $children   = array_merge($children, self::getChildren($region, $v['region_id']));
            }
        }

        return $children;
    }

    /**
     * 地区转换树形
     *
     * @param array   $region     所有地区
     * @param integer $region_pid 地区父级id
     * 
     * @return array
     */
    public static function toTree($region, $region_pid)
    {
        $tree = [];

        foreach ($region as $k => $v) {
            if ($v['region_pid'] == $region_pid) {
                $v['children'] = self::toTree($region, $v['region_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 地区模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'region_name')
    {
        $data = Db::name('region')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 地区精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'region_name')
    {
        $data = Db::name('region')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $data;
    }
}
