<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容管理
namespace app\common\service\cms;

use think\facade\Db;
use app\common\cache\cms\ContentCache;
use app\common\model\cms\CategoryModel;
use app\common\model\cms\ContentModel;
use app\common\service\file\FileService;

class ContentService
{
    // 表名
    protected static $t_name = 'cms_content';
    // 表主键
    protected static $t_pk = 'content_id';

    /**
     * 内容列表
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
            $field = self::$t_pk . ',category_id,name,img_ids,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time,delete_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', self::$t_pk => 'desc'];
        }

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $category = CategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            if (isset($v['category_id'])) {
                foreach ($category as $kp => $vp) {
                    if ($v['category_id'] == $vp['category_id']) {
                        $list[$k]['category_name'] = $vp['category_name'];
                    }
                }
            }

            $list[$k]['img_url'] = '';
            if (isset($v['img_ids'])) {
                $imgs = FileService::fileArray($v['img_ids']);
                if ($imgs) {
                    $list[$k]['img_url'] = $imgs[0]['file_url'];
                }
                unset($list[$k]['img_ids']);
            }
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 内容信息
     * 
     * @param $content_id 内容id
     * 
     * @return array|Exception
     */
    public static function info($content_id)
    {
        $content = ContentCache::get($content_id);
        if (empty($content)) {
            $where[] = [self::$t_pk, '=', $content_id];
            $content = Db::name(self::$t_name)
                ->where($where)
                ->find();
            if (empty($content)) {
                exception('内容不存在：' . $content_id);
            }

            $category = CategoryService::info($content['category_id']);

            $content['category_name'] = $category['category_name'];
            $content['imgs']          = FileService::fileArray($content['img_ids']);
            $content['files']         = FileService::fileArray($content['file_ids']);
            $content['videos']        = FileService::fileArray($content['video_ids']);

            ContentCache::set($content_id, $content);
        }

        // 点击量
        $gate = 10;
        $key  = $content[self::$t_pk] . 'hits';
        $hits = ContentCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name(self::$t_name)
                    ->where(self::$t_pk, '=', $content[self::$t_pk])
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    ContentCache::del($key);
                }
            } else {
                ContentCache::inc($key, 1);
            }
        } else {
            ContentCache::set($key, 1);
        }

        return $content;
    }

    /**
     * 内容添加
     *
     * @param $param 内容信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['img_ids']     = file_ids($param['imgs']);
        $param['file_ids']    = file_ids($param['files']);
        $param['video_ids']   = file_ids($param['videos']);
        $param['create_time'] = datetime();

        $content_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($content_id)) {
            exception();
        }

        $param[self::$t_pk] = $content_id;

        return $param;
    }

    /**
     * 内容修改 
     *     
     * @param $param 内容信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $content_id = $param[self::$t_pk];
        unset($param[self::$t_pk]);

        $param['img_ids']     = file_ids($param['imgs']);
        $param['file_ids']    = file_ids($param['files']);
        $param['video_ids']   = file_ids($param['videos']);
        $param['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $content_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ContentCache::del($content_id);

        $param[self::$t_pk] = $content_id;

        return $param;
    }

    /**
     * 内容删除
     * 
     * @param array $ids 内容列表
     * 
     * @return array|Exception
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

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容设置分类
     *
     * @param array   $ids         内容id
     * @param integer $category_id 分类id
     * 
     * @return array
     */
    public static function cate($ids, $category_id = 0)
    {
        $update['category_id'] = $category_id;
        $update['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容是否置顶
     *
     * @param array   $ids    内容id
     * @param integer $is_top 是否置顶
     * 
     * @return array
     */
    public static function istop($ids, $is_top = 0)
    {
        $update['is_top']      = $is_top;
        $update['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容是否热门
     *
     * @param array   $ids    内容id
     * @param integer $is_hot 是否热门
     * 
     * @return array
     */
    public static function ishot($ids, $is_hot = 0)
    {
        $update['is_hot']      = $is_hot;
        $update['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容是否推荐
     *
     * @param array   $ids    内容id
     * @param integer $is_rec 是否推荐
     * 
     * @return array
     */
    public static function isrec($ids, $is_rec = 0)
    {
        $update['is_rec']      = $is_rec;
        $update['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容是否隐藏
     *
     * @param array   $ids     内容id
     * @param integer $is_hide 是否隐藏
     * 
     * @return array
     */
    public static function ishide($ids, $is_hide = 0)
    {
        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $k => $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容上一条
     *
     * @param integer $content_id  内容id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条内容
     */
    public static function prev($content_id, $is_category = 0)
    {
        $where[] = [self::$t_pk, '<', $content_id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $content = self::info($content_id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $content = Db::name(self::$t_name)
            ->field('content_id,name')
            ->where($where)
            ->order(self::$t_pk, 'desc')
            ->find();
        if (empty($content)) {
            return [];
        }

        return $content;
    }

    /**
     * 内容下一条
     *
     * @param integer $content_id  内容id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条内容
     */
    public static function next($content_id, $is_category = 0)
    {
        $where[] = [self::$t_pk, '>', $content_id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $content = self::info($content_id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $content = Db::name(self::$t_name)
            ->field('content_id,name')
            ->where($where)
            ->order(self::$t_pk, 'asc')
            ->find();
        if (empty($content)) {
            return [];
        }

        return $content;
    }

    /**
     * 内容回收站恢复
     * 
     * @param array $ids 内容id
     * 
     * @return array|Exception
     */
    public static function recoverReco($ids)
    {
        $update['is_delete']   = 0;
        $update['update_time'] = datetime();
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 内容回收站删除
     * 
     * @param array $ids 内容id
     * 
     * @return array|Exception
     */
    public static function recoverDele($ids)
    {
        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            ContentCache::del($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key   = 'field';
        $field = ContentCache::get($key);
        if (empty($field)) {
            $sql = Db::name(self::$t_name)
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql   = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            ContentCache::set($key, $field);
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

        foreach ($fields as $v) {
            if ($v == $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * 内容统计
     *
     * @return array
     */
    public static function statistics()
    {
        $key  = 'count';
        $data = ContentCache::get($key);
        if (empty($data)) {
            $x_data = $s_data = $xs_data = [];

            $CmsContent = new ContentModel();
            $count = $CmsContent
                ->where('is_delete', 0)
                ->count(self::$t_pk);

            $CmsCategory = new CategoryModel();
            $category = $CmsCategory
                ->field('category_id,category_name')
                ->where('is_delete', 0)
                ->select()
                ->toArray();

            $content_count = $CmsContent
                ->field('category_id,count(category_id) as content_count')
                ->where('is_delete', 0)
                ->group('category_id')
                ->select()
                ->toArray();
            $xs_data = [];
            foreach ($category as $v) {
                $temp = [];
                $temp['x'] = $v['category_name'];
                $temp['s'] = 0;
                foreach ($content_count as $vc) {
                    if ($v['category_id'] == $vc['category_id']) {
                        $temp['s'] = $vc['content_count'];
                    }
                }
                $xs_data[] = $temp;
            }

            $ss = array_column($xs_data, 's');
            array_multisort($ss,  SORT_DESC, $xs_data);
            foreach ($xs_data as $v) {
                $x_data[] = $v['x'];
                $s_data[] = $v['s'];
            }

            $data['category'] = count($category);
            $data['count']    = $count;
            $data['x_data']   = $x_data;
            $data['s_data']   = $s_data;

            ContentCache::set($key, $data);
        }

        return $data;
    }
}
