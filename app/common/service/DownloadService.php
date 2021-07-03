<?php
/*
 * @Description  : 下载管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\DownloadCache;
use app\common\utils\ByteUtils;

class DownloadService
{
    /**
     * 下载列表
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
            $field = 'download_id,download_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'download_id,download_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['download_id' => 'desc'];
        }

        $count = Db::name('download')
            ->where($where)
            ->count('download_id');

        $list = Db::name('download')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $download_category = DownloadCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($download_category as $kp => $vp) {
                if ($v['download_category_id'] == $vp['download_category_id']) {
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
     * 下载信息
     * 
     * @param $download_id 下载id
     * 
     * @return array|Exception
     */
    public static function info($download_id)
    {
        $download = DownloadCache::get($download_id);

        if (empty($download)) {
            $download = Db::name('download')
                ->where('download_id', $download_id)
                ->find();
            if (empty($download)) {
                exception('下载不存在：' . $download_id);
            }

            $download_category = DownloadCategoryService::info($download['download_category_id']);

            $download['category_name'] = $download_category['category_name'];
            $download['imgs']          = file_unser($download['imgs']);
            $download['files']         = file_unser($download['files']);

            DownloadCache::set($download_id, $download);
        }

        // 点击量
        $gate = 10;
        $key = $download_id . 'Hits';
        $hits = DownloadCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name('download')
                    ->where('download_id', '=', $download_id)
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    DownloadCache::del($key);
                }
            } else {
                DownloadCache::inc($key, 1);
            }
        } else {
            DownloadCache::set($key, 1);
        }

        return $download;
    }

    /**
     * 下载添加
     *
     * @param $param 下载信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $download_id = Db::name('download')
            ->insertGetId($param);
        if (empty($download_id)) {
            exception();
        }

        $param['download_id'] = $download_id;
        $param['imgs']       = file_unser($param['imgs']);
        $param['files']      = file_unser($param['files']);

        return $param;
    }

    /**
     * 下载修改 
     *     
     * @param $param 下载信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $download_id = $param['download_id'];

        unset($param['download_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $res = Db::name('download')
            ->where('download_id', $download_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        DownloadCache::del($download_id);

        $param['download_id'] = $download_id;

        return $param;
    }

    /**
     * 下载删除
     * 
     * @param array $download 下载列表
     * 
     * @return array|Exception
     */
    public static function dele($download)
    {
        $download_ids = array_column($download, 'download_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }

    /**
     * 下载上传文件
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
            ->putFile('cms/download', $file, function () use ($type) {
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
     * 下载是否置顶
     *
     * @param array $param 下载信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $download     = $param['download'];
        $download_ids = array_column($download, 'download_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }

    /**
     * 下载是否热门
     *
     * @param array $param 下载信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $download     = $param['download'];
        $download_ids = array_column($download, 'download_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }

    /**
     * 下载是否推荐
     *
     * @param array $param 下载信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $download     = $param['download'];
        $download_ids = array_column($download, 'download_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }

    /**
     * 下载是否隐藏
     *
     * @param array $param 下载信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $download     = $param['download'];
        $download_ids = array_column($download, 'download_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }

    /**
     * 下载上一条
     *
     * @param integer $download_id  下载id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条下载
     */
    public static function prev($download_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['download_id', '<', $download_id];
        if ($is_category) {
            $download = self::info($download_id);
            $where[] = ['download_category_id', '=', $download['download_category_id']];
        }

        $download = Db::name('download')
            ->field('download_id,name')
            ->where($where)
            ->order('download_id', 'desc')
            ->find();
        if (empty($download)) {
            return [];
        }

        return $download;
    }

    /**
     * 下载下一条
     *
     * @param integer $download_id  下载id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条下载
     */
    public static function next($download_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['download_id', '>', $download_id];
        if ($is_category) {
            $download = self::info($download_id);
            $where[] = ['download_category_id', '=', $download['download_category_id']];
        }

        $download = Db::name('download')
            ->field('download_id,name')
            ->where($where)
            ->order('download_id', 'asc')
            ->find();
        if (empty($download)) {
            return [];
        }

        return $download;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = DownloadCache::get($key);
        if (empty($field)) {
            $sql = Db::name('download')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            DownloadCache::set($key, $field);
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
     * 下载回收站恢复
     * 
     * @param array $download 下载列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($download)
    {
        $download_ids = array_column($download, 'download_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }

    /**
     * 下载回收站删除
     * 
     * @param array $download 下载列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($download)
    {
        $download_ids = array_column($download, 'download_id');

        $res = Db::name('download')
            ->where('download_id', 'in', $download_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($download_ids as $k => $v) {
            DownloadCache::del($v);
        }

        $update['download_ids'] = $download_ids;

        return $update;
    }
}
