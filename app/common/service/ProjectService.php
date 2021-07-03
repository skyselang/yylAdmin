<?php
/*
 * @Description  : 案例管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\ProjectCache;
use app\common\utils\ByteUtils;

class ProjectService
{
    /**
     * 案例列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'project_id,project_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'project_id,project_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['project_id' => 'desc'];
        }

        $count = Db::name('project')
            ->where($where)
            ->count('project_id');

        $list = Db::name('project')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $project_category = ProjectCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($project_category as $kp => $vp) {
                if ($v['project_category_id'] == $vp['project_category_id']) {
                    $list[$k]['category_name'] = $vp['category_name'];
                }
            }

            $list[$k]['img_url'] = '';
            $imgs = file_unser($v['imgs']);
            if ($imgs) {
                $list[$k]['img_url'] = $imgs[0]['url'];
            }
            unset($list[$k]['imgs']);
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 案例信息
     * 
     * @param $project_id 案例id
     * 
     * @return array|Exception
     */
    public static function info($project_id)
    {
        $project = ProjectCache::get($project_id);

        if (empty($project)) {
            $project = Db::name('project')
                ->where('project_id', $project_id)
                ->find();
            if (empty($project)) {
                exception('案例不存在：' . $project_id);
            }

            $project_category = ProjectCategoryService::info($project['project_category_id']);

            $project['category_name'] = $project_category['category_name'];
            $project['imgs']          = file_unser($project['imgs']);
            $project['files']         = file_unser($project['files']);

            ProjectCache::set($project_id, $project);
        }

        // 点击量
        $gate = 10;
        $key = $project_id . 'Hits';
        $hits = ProjectCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name('project')
                    ->where('project_id', '=', $project_id)
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    ProjectCache::del($key);
                }
            } else {
                ProjectCache::inc($key, 1);
            }
        } else {
            ProjectCache::set($key, 1);
        }

        return $project;
    }

    /**
     * 案例添加
     *
     * @param $param 案例信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $project_id = Db::name('project')
            ->insertGetId($param);
        if (empty($project_id)) {
            exception();
        }

        $param['project_id'] = $project_id;
        $param['imgs']       = file_unser($param['imgs']);
        $param['files']      = file_unser($param['files']);

        return $param;
    }

    /**
     * 案例修改 
     *     
     * @param $param 案例信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $project_id = $param['project_id'];

        unset($param['project_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $res = Db::name('project')
            ->where('project_id', $project_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ProjectCache::del($project_id);

        $param['project_id'] = $project_id;

        return $param;
    }

    /**
     * 案例删除
     * 
     * @param array $project 案例列表
     * 
     * @return array|Exception
     */
    public static function dele($project)
    {
        $project_ids = array_column($project, 'project_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }

    /**
     * 案例上传文件
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
            ->putFile('cms/project', $file, function () use ($type) {
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
     * 案例是否置顶
     *
     * @param array $param 案例信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $project     = $param['project'];
        $project_ids = array_column($project, 'project_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }

    /**
     * 案例是否热门
     *
     * @param array $param 案例信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $project     = $param['project'];
        $project_ids = array_column($project, 'project_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }

    /**
     * 案例是否推荐
     *
     * @param array $param 案例信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $project     = $param['project'];
        $project_ids = array_column($project, 'project_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }

    /**
     * 案例是否隐藏
     *
     * @param array $param 案例信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $project     = $param['project'];
        $project_ids = array_column($project, 'project_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }

    /**
     * 案例上一条
     *
     * @param integer $project_id  案例id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条案例
     */
    public static function prev($project_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['project_id', '<', $project_id];
        if ($is_category) {
            $project = self::info($project_id);
            $where[] = ['project_category_id', '=', $project['project_category_id']];
        }

        $project = Db::name('project')
            ->field('project_id,name')
            ->where($where)
            ->order('project_id', 'desc')
            ->find();
        if (empty($project)) {
            return [];
        }

        return $project;
    }

    /**
     * 案例下一条
     *
     * @param integer $project_id  案例id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条案例
     */
    public static function next($project_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['project_id', '>', $project_id];
        if ($is_category) {
            $project = self::info($project_id);
            $where[] = ['project_category_id', '=', $project['project_category_id']];
        }

        $project = Db::name('project')
            ->field('project_id,name')
            ->where($where)
            ->order('project_id', 'asc')
            ->find();
        if (empty($project)) {
            return [];
        }

        return $project;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = ProjectCache::get($key);
        if (empty($field)) {
            $sql = Db::name('project')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            ProjectCache::set($key, $field);
        }

        return $field;
    }

    /**
     * 表字段是否存在
     * 
     * @param string $field 要检查的字段
     * 
     * @return bool
     */
    public static function tableFieldExist($field)
    {
        $fields = self::tableField();

        foreach ($fields as $k => $v) {
            if ($v == $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * 案例回收站恢复
     * 
     * @param array $project 案例列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($project)
    {
        $project_ids = array_column($project, 'project_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }

    /**
     * 案例回收站删除
     * 
     * @param array $project 案例列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($project)
    {
        $project_ids = array_column($project, 'project_id');

        $res = Db::name('project')
            ->where('project_id', 'in', $project_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($project_ids as $k => $v) {
            ProjectCache::del($v);
        }

        $update['project_ids'] = $project_ids;

        return $update;
    }
}
