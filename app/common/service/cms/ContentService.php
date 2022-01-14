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

use app\common\cache\cms\ContentCache;
use app\common\model\cms\CategoryModel;
use app\common\model\cms\ContentModel;
use app\common\service\file\FileService;

class ContentService
{
    /**
     * 内容列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',category_id,name,img_ids,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time,delete_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

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
     * @param $id 内容id
     * 
     * @return array|Exception
     */
    public static function info($id)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $info = ContentCache::get($id);
        if (empty($info)) {
            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                exception('内容不存在：' . $id);
            }
            $info = $info->toArray();

            $info['category_name'] = '';
            if ($info['category_id']) {
                $category = CategoryService::info($info['category_id']);
                $info['category_name'] = $category['category_name'];
            }
            $info['imgs']   = FileService::fileArray($info['img_ids']);
            $info['files']  = FileService::fileArray($info['file_ids']);
            $info['videos'] = FileService::fileArray($info['video_ids']);

            ContentCache::set($id, $info);
        }

        // 点击量
        $gate = 10;
        $key  = $info[$pk] . 'hits';
        $hits = ContentCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = $model->where($pk, $info[$pk])->inc('hits', $hits)->update();
                if ($res) {
                    ContentCache::del($key);
                }
            } else {
                ContentCache::inc($key, 1);
            }
        } else {
            ContentCache::set($key, 1);
        }

        return $info;
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
        $model = new ContentModel();
        $pk = $model->getPk();

        $param['img_ids']     = file_ids($param['imgs']);
        $param['file_ids']    = file_ids($param['files']);
        $param['video_ids']   = file_ids($param['videos']);
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

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
        $model = new ContentModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['img_ids']     = file_ids($param['imgs']);
        $param['file_ids']    = file_ids($param['files']);
        $param['video_ids']   = file_ids($param['videos']);
        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        ContentCache::del($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 内容删除
     * 
     * @param array $ids 内容id
     * 
     * @return array|Exception
     */
    public static function dele($ids)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
     * @param array $ids         内容id
     * @param int   $category_id 分类id
     * 
     * @return array
     */
    public static function cate($ids, $category_id = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['category_id'] = $category_id;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
     * @param array $ids    内容id
     * @param int   $is_top 是否置顶
     * 
     * @return array
     */
    public static function istop($ids, $is_top = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['is_top']      = $is_top;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
     * @param array $ids    内容id
     * @param int   $is_hot 是否热门
     * 
     * @return array
     */
    public static function ishot($ids, $is_hot = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['is_hot']      = $is_hot;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
     * @param array $ids    内容id
     * @param int   $is_rec 是否推荐
     * 
     * @return array
     */
    public static function isrec($ids, $is_rec = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['is_rec']      = $is_rec;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
     * @param array $ids     内容id
     * @param int   $is_hide 是否隐藏
     * 
     * @return array
     */
    public static function ishide($ids, $is_hide = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['is_hide']     = $is_hide;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
     * 内容上一条
     *
     * @param int $id          内容id
     * @param int $is_category 是否当前分类
     * 
     * @return array 上一条内容
     */
    public static function prev($id, $is_category = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $where[] = [$pk, '<', $id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $content = self::info($id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $info = $model->field($pk . ',name')->where($where)->order($pk, 'desc')->find();
        if (empty($info)) {
            return [];
        }
        $info = $info->toArray();

        return $info;
    }

    /**
     * 内容下一条
     *
     * @param int $id          内容id
     * @param int $is_category 是否当前分类
     * 
     * @return array 下一条内容
     */
    public static function next($id, $is_category = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $where[] = [$pk, '>', $id];
        $where[] = ['is_delete', '=', 0];
        if ($is_category) {
            $content = self::info($id);
            $where[] = ['category_id', '=', $content['category_id']];
        }

        $info = $model->field($pk . ',name')->where($where)->order($pk, 'asc')->find();
        if (empty($info)) {
            return [];
        }
        $info = $info->toArray();

        return $info;
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
        $model = new ContentModel();
        $pk = $model->getPk();

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
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
        $model = new ContentModel();
        $pk = $model->getPk();

        $res = $model->where($pk, 'in', $ids)->delete();
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
     * 内容统计
     *
     * @return array
     */
    public static function statistics()
    {
        $key  = 'count';
        $data = ContentCache::get($key);
        if (empty($data)) {
            $CategoryModel = new CategoryModel();
            $CategoryPk = $CategoryModel->getPk();
            $category = $CategoryModel->field($CategoryPk . ',category_name')->where('is_delete', 0)->select()->toArray();

            $ContentModel = new ContentModel();
            $ContentPk = $ContentModel->getPk();

            $count = $ContentModel->where('is_delete', 0)->count($ContentPk);
            $field = $CategoryPk . ',count(' . $CategoryPk . ') as count';
            $content = $ContentModel->field($field)->where('is_delete', 0)->group($CategoryPk)->select()->toArray();

            $x_data = $s_data = $xs_data = [];
            foreach ($category as $v) {
                $temp = [];
                $temp['x'] = $v['category_name'];
                $temp['s'] = 0;
                foreach ($content as $vc) {
                    if ($v[$CategoryPk] == $vc[$CategoryPk]) {
                        $temp['s'] = $vc['count'];
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
            $data['content']  = $count;
            $data['x_data']   = $x_data;
            $data['s_data']   = $s_data;

            ContentCache::set($key, $data);
        }

        return $data;
    }
}
