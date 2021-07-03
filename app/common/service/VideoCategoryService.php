<?php
/*
 * @Description  : 视频分类业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-28
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\VideoCategoryCache;
use app\common\utils\ByteUtils;

class VideoCategoryService
{
    protected static $allkey = 'all';

    /**
     * 视频分类列表
     * 
     * @param string $type tree树形list列表
     * 
     * @return array
     */
    public static function list($type = 'tree')
    {
        $key = self::$allkey;
        $data = VideoCategoryCache::get($key);
        if (empty($data)) {
            $field = 'video_category_id,video_category_pid,category_name,sort,is_hide,create_time,update_time';

            $where[] = ['is_delete', '=', 0];

            $order = ['sort' => 'desc', 'video_category_id' => 'desc'];

            $list = Db::name('video_category')
                ->field($field)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();

            $data['list'] = $list;
            $data['tree'] = self::toTree($list, 0);

            VideoCategoryCache::set($key, $data);
        }

        if ($type == 'list') {
            return $data['list'];
        }

        return $data['tree'];
    }

    /**
     * 视频分类信息
     * 
     * @param integer $video_category_id 视频分类id
     * 
     * @return array|Exception
     */
    public static function info($video_category_id)
    {
        $video_category = VideoCategoryCache::get($video_category_id);
        if (empty($video_category)) {
            $video_category = Db::name('video_category')
                ->where('video_category_id', $video_category_id)
                ->find();
            if (empty($video_category)) {
                exception('视频分类不存在：' . $video_category_id);
            }
            
            $video_category['imgs'] = file_unser($video_category['imgs']);

            VideoCategoryCache::set($video_category_id, $video_category);
        }

        return $video_category;
    }

    /**
     * 视频分类添加
     *
     * @param array $param 视频分类信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $video_category_id = Db::name('video_category')
            ->insertGetId($param);
        if (empty($video_category_id)) {
            exception();
        }

        VideoCategoryCache::del(self::$allkey);

        $param['video_category_id'] = $video_category_id;

        return $param;
    }

    /**
     * 视频分类修改 
     *     
     * @param array $param 视频分类信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $video_category_id = $param['video_category_id'];

        unset($param['video_category_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);

        $res = Db::name('video_category')
            ->where('video_category_id', $video_category_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        VideoCategoryCache::del(self::$allkey);
        VideoCategoryCache::del($video_category_id);

        $param['video_category_id'] = $video_category_id;

        return $param;
    }

    /**
     * 视频分类删除
     * 
     * @param array $video_category 视频分类
     * 
     * @return array|Exception
     */
    public static function dele($video_category)
    {
        $video_category_ids = array_column($video_category, 'video_category_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('video_category')
            ->where('video_category_id', 'in', $video_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_category_ids as $k => $v) {
            VideoCategoryCache::del($v);
        }
        VideoCategoryCache::del(self::$allkey);

        $update['video_category_ids'] = $video_category_ids;

        return $update;
    }

    /**
     * 视频分类上传图片
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
            ->putFile('cms/video_category', $file, function () use ($type) {
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
     * 视频分类是否隐藏
     *
     * @param array $param 视频分类
     * 
     * @return array|Exception
     */
    public static function ishide($param)
    {
        $video_category     = $param['video_category'];
        $video_category_ids = array_column($video_category, 'video_category_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('video_category')
            ->where('video_category_id', 'in', $video_category_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_category_ids as $k => $v) {
            VideoCategoryCache::del($v);
        }
        VideoCategoryCache::del(self::$allkey);

        $update['video_category_ids'] = $video_category_ids;

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
        $video_category_id  = isset($data['video_category_id']) ? $data['video_category_id'] : '';
        $video_category_pid = isset($data['video_category_pid']) ? $data['video_category_pid'] : 0;
        $category_name        = $data['category_name'];
        if ($video_category_id) {
            if ($video_category_id == $video_category_pid) {
                return '分类父级不能等于分类本身';
            }
            $where[] = ['video_category_id', '<>', $video_category_id];
        }

        $where[] = ['video_category_pid', '=', $video_category_pid];
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $res = Db::name('video_category')
            ->field('video_category_id')
            ->where($where)
            ->find();
        if ($res) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    /**
     * 视频分类列表转树形
     *
     * @param array   $video_category     视频分类列表
     * @param integer $video_category_pid 视频分类父级id
     * 
     * @return array
     */
    public static function toTree($video_category, $video_category_pid)
    {
        $tree = [];

        foreach ($video_category as $k => $v) {
            if ($v['video_category_pid'] == $video_category_pid) {
                $v['children'] = self::toTree($video_category, $v['video_category_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
