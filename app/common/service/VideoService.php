<?php
/*
 * @Description  : 视频管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\VideoCache;
use app\common\utils\ByteUtils;

class VideoService
{
    /**
     * 视频列表
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
            $field = 'video_id,video_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'video_id,video_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['video_id' => 'desc'];
        }

        $count = Db::name('video')
            ->where($where)
            ->count('video_id');

        $list = Db::name('video')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $video_category = VideoCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($video_category as $kp => $vp) {
                if ($v['video_category_id'] == $vp['video_category_id']) {
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
     * 视频信息
     * 
     * @param $video_id 视频id
     * 
     * @return array|Exception
     */
    public static function info($video_id)
    {
        $video = VideoCache::get($video_id);

        if (empty($video)) {
            $video = Db::name('video')
                ->where('video_id', $video_id)
                ->find();
            if (empty($video)) {
                exception('视频不存在：' . $video_id);
            }

            $video_category = VideoCategoryService::info($video['video_category_id']);

            $video['category_name'] = $video_category['category_name'];
            $video['imgs']          = file_unser($video['imgs']);
            $video['videos']        = file_unser($video['videos']);
            $video['files']         = file_unser($video['files']);

            VideoCache::set($video_id, $video);
        }

        // 点击量
        $gate = 10;
        $key = $video_id . 'Hits';
        $hits = VideoCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name('video')
                    ->where('video_id', '=', $video_id)
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    VideoCache::del($key);
                }
            } else {
                VideoCache::inc($key, 1);
            }
        } else {
            VideoCache::set($key, 1);
        }

        return $video;
    }

    /**
     * 视频添加
     *
     * @param $param 视频信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['videos']      = file_ser($param['videos']);
        $param['files']       = file_ser($param['files']);

        $video_id = Db::name('video')
            ->insertGetId($param);
        if (empty($video_id)) {
            exception();
        }

        $param['video_id'] = $video_id;
        $param['imgs']       = file_unser($param['imgs']);
        $param['files']      = file_unser($param['files']);

        return $param;
    }

    /**
     * 视频修改 
     *     
     * @param $param 视频信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $video_id = $param['video_id'];

        unset($param['video_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['videos']      = file_ser($param['videos']);
        $param['files']       = file_ser($param['files']);

        $res = Db::name('video')
            ->where('video_id', $video_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        VideoCache::del($video_id);

        $param['video_id'] = $video_id;

        return $param;
    }

    /**
     * 视频删除
     * 
     * @param array $video 视频列表
     * 
     * @return array|Exception
     */
    public static function dele($video)
    {
        $video_ids = array_column($video, 'video_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }

    /**
     * 视频上传文件
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
            ->putFile('cms/video', $file, function () use ($type) {
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
     * 视频是否置顶
     *
     * @param array $param 视频信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $video     = $param['videos'];
        $video_ids = array_column($video, 'video_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }

    /**
     * 视频是否热门
     *
     * @param array $param 视频信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $video     = $param['videos'];
        $video_ids = array_column($video, 'video_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }

    /**
     * 视频是否推荐
     *
     * @param array $param 视频信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $video     = $param['videos'];
        $video_ids = array_column($video, 'video_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }

    /**
     * 视频是否隐藏
     *
     * @param array $param 视频信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $video     = $param['videos'];
        $video_ids = array_column($video, 'video_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }

    /**
     * 视频上一条
     *
     * @param integer $video_id  视频id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条视频
     */
    public static function prev($video_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['video_id', '<', $video_id];
        if ($is_category) {
            $video = self::info($video_id);
            $where[] = ['video_category_id', '=', $video['video_category_id']];
        }

        $video = Db::name('video')
            ->field('video_id,name')
            ->where($where)
            ->order('video_id', 'desc')
            ->find();
        if (empty($video)) {
            return [];
        }

        return $video;
    }

    /**
     * 视频下一条
     *
     * @param integer $video_id  视频id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条视频
     */
    public static function next($video_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['video_id', '>', $video_id];
        if ($is_category) {
            $video = self::info($video_id);
            $where[] = ['video_category_id', '=', $video['video_category_id']];
        }

        $video = Db::name('video')
            ->field('video_id,name')
            ->where($where)
            ->order('video_id', 'asc')
            ->find();
        if (empty($video)) {
            return [];
        }

        return $video;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = VideoCache::get($key);
        if (empty($field)) {
            $sql = Db::name('video')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            VideoCache::set($key, $field);
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
     * 视频回收站恢复
     * 
     * @param array $video 视频列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($video)
    {
        $video_ids = array_column($video, 'video_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }

    /**
     * 视频回收站删除
     * 
     * @param array $video 视频列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($video)
    {
        $video_ids = array_column($video, 'video_id');

        $res = Db::name('video')
            ->where('video_id', 'in', $video_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($video_ids as $k => $v) {
            VideoCache::del($v);
        }

        $update['video_ids'] = $video_ids;

        return $update;
    }
}
