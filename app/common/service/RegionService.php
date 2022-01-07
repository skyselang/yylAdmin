<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 地区管理
namespace app\common\service;

use think\facade\Db;
use app\common\cache\RegionCache;
use Overtrue\Pinyin\Pinyin;

class RegionService
{
    // 表名
    protected static $t_name = 'region';
    // 表主键
    protected static $t_pk = 'region_id';
    // 树形key
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
            $field = self::$t_pk . ',region_pid,region_path,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_sort';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['region_sort' => 'desc', self::$t_pk => 'asc'];
        }

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->order($order)
            ->select()
            ->toArray();

        $count = count($list);

        foreach ($list as $k => $v) {
            $v['children']    = [];
            $v['hasChildren'] = true;
            $list[$k] = $v;
        }

        return compact('count', 'list');
    }

    /**
     * 地区信息
     *
     * @param mixed $region_id 地区id（tree树形）
     * 
     * @return array
     */
    public static function info($region_id)
    {
        $region = RegionCache::get($region_id);
        if (empty($region)) {
            if ($region_id == self::$tree_key) {
                $region = Db::name(self::$t_name)
                    ->field('region_id,region_pid,region_name')
                    ->where('is_delete', '=', 0)
                    ->select()
                    ->toArray();

                $region = self::toTree($region, 0);
            } else {
                $region = Db::name(self::$t_name)
                    ->where(self::$t_pk, $region_id)
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
                    foreach ($region_path as $v) {
                        $region_pid[] = Db::name(self::$t_name)
                            ->field('region_name,region_pinyin')
                            ->where(self::$t_pk, '=', $v)
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
    public static function add($param)
    {
        $param = self::pinyin($param);
        $param['create_time'] = datetime();

        $res = false;
        // 启动事务
        Db::startTrans();
        try {
            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);

                $param['region_level'] = $region['region_level'] + 1;
                $region_id = Db::name(self::$t_name)
                    ->insertGetId($param);

                $region_path = $region['region_path'] . ',' . $region_id;
            } else {
                $region_id = Db::name(self::$t_name)
                    ->insertGetId($param);

                $region_path = $region_id;
                $update['region_path'] = $region_path;
            }

            $update['region_path'] = $region_path;
            $res = Db::name(self::$t_name)
                ->where(self::$t_pk, $region_id)
                ->update($update);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        if (empty($res)) {
            exception();
        }

        RegionCache::del(self::$tree_key);

        $param[self::$t_pk]   = $region_id;
        $param['region_path'] = $region_path;

        return $param;
    }

    /**
     * 地区修改
     *
     * @param array $param 地区信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $param = self::pinyin($param);

        $region_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $res = false;
        // 启动事务
        Db::startTrans();
        try {
            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);

                $param['region_level'] = $region['region_level'] + 1;
                Db::name(self::$t_name)
                    ->where(self::$t_pk, $region_id)
                    ->update($param);

                $region_path = $region['region_path'] . ',' . $region_id;
                $update['region_path'] = $region_path;
            } else {
                Db::name(self::$t_name)
                    ->where(self::$t_pk, $region_id)
                    ->update($param);

                $region_path = $region_id;
                $update['region_path'] = $region_path;
            }

            $update['update_time'] = datetime();
            $res = Db::name(self::$t_name)
                ->where(self::$t_pk, $region_id)
                ->update($update);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk]   = $region_id;
        $param['region_path'] = $region_path;

        RegionCache::del(self::$tree_key);
        RegionCache::del($region_id);

        return $param;
    }

    /**
     * 地区设置父级
     *
     * @param array   $ids        地区id
     * @param integer $region_pid 地区父级id
     * 
     * @return array
     */
    public static function pid($ids, $region_pid)
    {
        $errmsg = '';
        // 启动事务
        Db::startTrans();
        try {
            $update['region_pid']  = $region_pid;
            $update['update_time'] = datetime();
            foreach ($ids as $v) {
                $region_level = 1;
                $region_path  = $v;
                if ($region_pid) {
                    $region = self::info($region_pid);
                    $region_level = $region['region_level'] + 1;
                    $region_path  = $region['region_path'] . ',' . $v;
                }
                $update['region_level'] = $region_level;
                $update['region_path']  = $region_path;
                Db::name(self::$t_name)
                    ->where(self::$t_pk, '=', $v)
                    ->update($update);
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            Db::rollback();
        }

        if ($errmsg) {
            exception($errmsg);
        }

        foreach ($ids as $v) {
            RegionCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 地区删除
     *
     * @param array $ids 地区id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        RegionCache::del(self::$tree_key);
        foreach ($ids as $v) {
            RegionCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 地区获取所有子级
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
                $children[] = $v[self::$t_pk];
                $children   = array_merge($children, self::getChildren($region, $v[self::$t_pk]));
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
                $v['children'] = self::toTree($region, $v[self::$t_pk]);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 地区拼音，简拼，首字母
     *
     * @param array $param
     *
     * @return array
     */
    public static function pinyin($param)
    {
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

        return $param;
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
        $data = Db::name(self::$t_name)
            ->where($field, 'like', '%' . $keyword . '%')
            ->where('is_delete', '=', 0)
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
    public static function equQuery($keyword, $field = 'region_name')
    {
        $data = Db::name(self::$t_name)
            ->where($field, '=', $keyword)
            ->where('is_delete', '=', 0)
            ->select()
            ->toArray();

        return $data;
    }
}
