<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use Overtrue\Pinyin\Pinyin;
use app\common\cache\setting\RegionCache;
use app\common\model\setting\RegionModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 地区设置
 */
class RegionService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'region_id/d'        => '',
        'region_pid/d'       => 0,
        'region_level/d'     => 1,
        'region_name/s'      => '',
        'region_pinyin/s'    => '',
        'region_jianpin/s'   => '',
        'region_initials/s'  => '',
        'region_citycode/s'  => '',
        'region_zipcode/s'   => '',
        'region_longitude/s' => '',
        'region_latitude/s'  => '',
        'sort/d'             => 2250,
    ];

    /**
     * 地区列表
     * 
     * @param string $type  list列表，tree树形
     * @param array  $where 条件
     * @param array  $order 排序
     * @param string $field 字段
     * @param int    $level 级别：1省2市3区4县街道乡镇
     *
     * @return array 
     */
    public static function list($type = 'list', $where = [], $order = [], $field = '', $level = 3)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',region_pid,region_name,region_pinyin,region_citycode,region_zipcode,region_longitude,region_latitude,sort';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'asc'];
        }

        $where[] = ['region_level', '<=', $level];

        $key = where_cache_key($type, $where, $order, $field);
        $data = RegionCache::get($key);
        if (empty($data)) {
            $data = $model->field($field)->where($where)->order($order)->select()->toArray();
            if ($type == 'list') {
                foreach ($data as &$v) {
                    $v['children']    = [];
                    $v['hasChildren'] = true;
                }
                $data = array_to_tree($data, $pk, 'region_pid');
            } else {
                $data = list_to_tree($data, $pk, 'region_pid');
            }
            RegionCache::set($key, $data);
        }
        return $data;
    }

    /**
     * 地区信息
     *
     * @param int  $id   地区id
     * @param bool $exce 不存在是否抛出异常
     * @Apidoc\Returned("region_fullname", type="string", desc="地区完整名称")
     * @Apidoc\Returned("region_fullname_py", type="string", desc="地区完整名称拼音")
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = RegionCache::get($id);
        if (empty($info)) {
            $model = new RegionModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('地区不存在：' . $id);
                }
                return [];
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

            RegionCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 地区添加
     *
     * @param array $param 地区信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            $param = self::pinyin($param);
            if (isset($param['region_pid'])) {
                if (empty($param['region_pid'])) {
                    $param['region_pid'] = 0;
                }
            }
            if (isset($param['region_level'])) {
                if (empty($param['region_level'])) {
                    $param['region_level'] = 1;
                }
            }

            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);
                if ($region['is_delete']) {
                    $param['is_delete'] = 1;
                }
                $param['region_level'] = $region['region_level'] + 1;
                $model->save($param);
                $region_id = $model->$pk;
                $region_path = $region['region_path'] . ',' . $region_id;
            } else {
                $model->save($param);
                $region_id = $model->$pk;
                $region_path = $region_id;
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

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk]           = $region_id;
        $param['region_path'] = $region_path;

        RegionCache::clear();

        return $param;
    }

    /**
     * 地区修改
     *
     * @param int   $id    地区id
     * @param array $param 地区信息
     * 
     * @return array|Exception
     */
    public static function edit($id, $param)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            $param = self::pinyin($param);
            if (isset($param['region_pid'])) {
                if (empty($param['region_pid'])) {
                    $param['region_pid'] = 0;
                }
            }
            if (isset($param['region_level'])) {
                if (empty($param['region_level'])) {
                    $param['region_level'] = 1;
                }
            }

            if ($param['region_pid']) {
                $region = self::info($param['region_pid']);
                $param['region_level'] = $region['region_level'] + 1;
                $param['region_path']  = $region['region_path'] . ',' . $id;
            } else {
                $param['region_path'] = $id;
            }

            $model->where($pk, $id)->update($param);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $id;

        RegionCache::clear();

        return $param;
    }

    /**
     * 地区删除
     *
     * @param array $ids  地区id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = delete_update();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        RegionCache::clear();

        return $update;
    }

    /**
     * 地区修改上级
     *
     * @param array $ids        地区id
     * @param int   $region_pid 地区pid
     * 
     * @return array|Exception
     */
    public static function editpid($ids, $region_pid)
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            $update['region_pid']  = $region_pid;
            $update['update_uid']  = user_id();
            $update['update_time'] = datetime();
            $region = [];
            if ($region_pid) {
                $region = self::info($region_pid);
            }
            foreach ($ids as $v) {
                $region_level = 1;
                $region_path  = $v;
                if ($region) {
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

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        RegionCache::clear();

        return $update;
    }

    /**
     * 地区更新
     *
     * @param array $ids   地区id
     * @param array $param 地区信息
     * 
     * @return array|Exception
     */
    public static function update($ids, $param = [])
    {
        $model = new RegionModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        RegionCache::clear();

        return $param;
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
