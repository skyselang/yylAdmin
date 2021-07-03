<?php
/*
 * @Description  : 下载分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-28
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\DownloadCategoryCache;
use app\common\utils\ByteUtils;

class DownloadCategoryService
{
    protected static $allkey = 'all';

    /**
     * 下载分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$allkey;
        $data = DownloadCategoryCache::get($key);
        if (empty($data)) {
            $field = 'download_category_id,download_category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'download_category_id' => 'desc'];

            $list = Db::name('download_category')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            DownloadCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 下载分类信息
     * 
     * @param integer $download_category_id 下载分类id
     * 
     * @return array|Exception
     */
    public static function info($download_category_id)
    {
        $download_category = DownloadCategoryCache::get($download_category_id);
        if (empty($download_category)) {
            $download_category = Db::name('download_category')
                ->where('download_category_id', $download_category_id)
                ->find();
            if (empty($download_category)) {
                exception('下载分类不存在：' . $download_category_id);
            }
            
            $download_category['imgs'] = file_unser($download_category['imgs']);

            DownloadCategoryCache::set($download_category_id, $download_category);
        }

        return $download_category;
    }

    /**
     * 下载分类添加
     *
     * @param array $param 下载分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $download_category_id = Db::name('download_category')
            ->insertGetId($param);
        if (empty($download_category_id)) {
            exception();
        }

        DownloadCategoryCache::del(self::$allkey);

        $param['download_category_id'] = $download_category_id;

        return $param;
    }

    /**
     * 下载分类修改 
     *     
     * @param array $param 下载分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $download_category_id = $param['download_category_id'];

        unset($param['download_category_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $res = Db::name('download_category')
            ->where('download_category_id', $download_category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        DownloadCategoryCache::del(self::$allkey);
        DownloadCategoryCache::del($download_category_id);

        $param['download_category_id'] = $download_category_id;

        return $param;
    }

    /**
     * 下载分类删除
     * 
     * @param array $download_category 下载分类
     * 
     * @return array|Exception
     */
    public static function dele($download_category)
    {
        $download_category_ids = array_column($download_category, 'download_category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('download_category')
            ->where('download_category_id', 'in', $download_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_category_ids as $k => $v) {
            DownloadCategoryCache::del($v);
        }
        DownloadCategoryCache::del(self::$allkey);

        $update['download_category_ids'] = $download_category_ids;

        return $update;
    }

    /**
     * 下载分类上传图片
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
            ->putFile('cms/download_category', $file, function () use ($type) {
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
     * 下载分类是否隐藏
     *
     * @param array $param 下载分类
     * 
     * @return array|Exception
     */
    public static function ishide($param)
    {
        $download_category     = $param['download_category'];
        $download_category_ids = array_column($download_category, 'download_category_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('download_category')
            ->where('download_category_id', 'in', $download_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($download_category_ids as $k => $v) {
            DownloadCategoryCache::del($v);
        }
        DownloadCategoryCache::del(self::$allkey);

        $update['download_category_ids'] = $download_category_ids;

        return $update;
    }

    /**
     * 分类名称是否已存在
     *
     * @param array $data 分类数据
     *
     * @return bool
     */
    public static function checkCategoryName($data)
    {
        $download_category_id  = isset($data['download_category_id']) ? $data['download_category_id'] : '';
        $download_category_pid = isset($data['download_category_pid']) ? $data['download_category_pid'] : 0;
        $category_name        = $data['category_name'];
        if ($download_category_id) {
            if ($download_category_id == $download_category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = ['download_category_id', '<>', $download_category_id];
        }

        $where[] = ['download_category_pid', '=', $download_category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name('download_category')
            ->field('download_category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    /**
     * 下载分类列表转树形
     *
     * @param array   $download_category     下载分类列表
     * @param integer $download_category_pid 下载分类父级id
     * 
     * @return array
     */
    public static function toTree($download_category, $download_category_pid)
    {
        $tree = [];

        foreach ($download_category as $k => $v) {
            if ($v['download_category_pid'] == $download_category_pid) {
                $v['children'] = self::toTree($download_category, $v['download_category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
