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

use app\common\cache\RegionCache;
use app\common\model\RegionModel;
use Overtrue\Pinyin\Pinyin;

class RegionService
{
    // 树形key
    protected static $tree = 'tree';

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
        $model = new RegionModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',region_pid,region_path,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_sort';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['region_sort' => 'desc', $pk => 'asc'];
        }

        $list = $model->field($field)->where($where)->order($order)->select()->toArray();

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
     * @param mixed $id 地区id、树形key
     * 
     * @return array
     */
    public static function info($id)
    {
        $info = RegionCache::get($id);
        if (empty($info)) {
            $model = new RegionModel();
            $pk = $model->getPk();

            if ($id == self::$tree) {
                $info = $model->field($pk . ',region_pid,region_name')->where('is_delete', 0)->select()->toArray();
                $info = self::toTree($info, 0);
            } else {
                $info = $model->where($pk, $id)->find();
                if (empty($info)) {
                    exception('地区不存在：' . $id);
                }
                $info = $info->toArray();

                // 地区完整名称
                $region_path = explode(',', $info['region_path']);
                if (count($region_path) == 1) {
                    $region_fullname    = $info['region_name'];
                    $region_fullname_py = $info['region_pinyin'];
                } else {
                    $region_pid = [];
                    foreach ($region_path as $v) {
                        $region_pid[] = $model->field('region_name,region_pinyin')->where($pk, $v)->find();
                    }
                    $region_fullname    = array_column($region_pid, 'region_name');
                    $region_fullname    = implode('-', $region_fullname);
                    $region_fullname_py = array_column($region_pid, 'region_pinyin');
                    $region_fullname_py = implode('-', $region_fullname_py);
                }
                $info['region_fullname']    = $region_fullname;
                $info['region_fullname_py'] = $region_fullname_py;
            }

            RegionCache::set($id, $info);
        }

        return $info;
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
        $model = new RegionModel();
        $pk = $model->getPk();

        $param = self::pinyin($param);
        $param['create_time'] = datetime();

        $errmsg = '';
        // 启动事务
        $model->startTrans();
        try {
            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);
                $param['region_level'] = $region['region_level'] + 1;
                $region_id = $model->insertGetId($param);
                $region_path = $region['region_path'] . ',' . $region_id;
            } else {
                $region_id = $model->insertGetId($param);
                $region_path = $region_id;
                $update['region_path'] = $region_path;
            }

            $update['region_path'] = $region_path;
            $model->where($pk, $region_id)->update($update);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (empty($errmsg)) {
            exception('添加失败：' . $errmsg);
        }

        RegionCache::del(self::$tree);

        $param[$pk]           = $region_id;
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
        $model = new RegionModel();
        $pk = $model->getPk();

        $param = self::pinyin($param);

        $region_id = $param[$pk];
        unset($param[$pk]);

        $errmsg = '';
        // 启动事务
        $model->startTrans();
        try {
            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);

                $param['region_level'] = $region['region_level'] + 1;
                $model->where($pk, $region_id)->update($param);

                $region_path = $region['region_path'] . ',' . $region_id;
                $update['region_path'] = $region_path;
            } else {
                $model->where($pk, $region_id)->update($param);

                $region_path = $region_id;
                $update['region_path'] = $region_path;
            }

            $update['update_time'] = datetime();
            $model->where($pk, $region_id)->update($update);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (empty($errmsg)) {
            exception('修改失败：' . $errmsg);
        }

        RegionCache::del($region_id);
        RegionCache::del(self::$tree);

        $param[$pk]           = $region_id;
        $param['region_path'] = $region_path;

        return $param;
    }

    /**
     * 地区设置父级
     *
     * @param array $ids        地区id
     * @param int   $region_pid 地区pid
     * 
     * @return array
     */
    public static function pid($ids, $region_pid)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        $errmsg = '';
        // 启动事务
        $model->startTrans();
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
                $model->where($pk, $v)->update($update);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if ($errmsg) {
            exception('设置失败：' . $errmsg);
        }

        foreach ($ids as $v) {
            RegionCache::del($v);
        }
        RegionCache::del(self::$tree);

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
        $model = new RegionModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            RegionCache::del($v);
        }
        RegionCache::del(self::$tree);

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 地区获取所有子级
     *
     * @param array $region    所有地区
     * @param int   $region_id 地区id
     * 
     * @return array
     */
    public static function getChildren($region, $region_id)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        $children = [];
        foreach ($region as $v) {
            if ($v['region_pid'] == $region_id) {
                $children[] = $v[$pk];
                $children   = array_merge($children, self::getChildren($region, $v[$pk]));
            }
        }

        return $children;
    }

    /**
     * 地区转换树形
     *
     * @param array $region     所有地区
     * @param int   $region_pid 地区pid
     * 
     * @return array
     */
    public static function toTree($region, $region_pid)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        $tree = [];
        foreach ($region as $v) {
            if ($v['region_pid'] == $region_pid) {
                $v['children'] = self::toTree($region, $v[$pk]);
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
}
