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

        $CategoryModel = new CategoryModel();
        $CategoryPk = $CategoryModel->getPk();

        if (empty($field)) {
            $field = $pk . ',' . $CategoryPk . ',name,img_id,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time,delete_time';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        $file_ids = array_column($list, 'img_id', 'img_id');
        $category_ids = array_column($list, $CategoryPk);

        $file = array_column(FileService::fileArray($file_ids), 'file_url', 'file_id');
        $category = $CategoryModel->where($CategoryPk, 'in', $category_ids)->column('category_name', $CategoryPk);

        foreach ($list as $k => $v) {
            $list[$k]['img_url'] = $file[$v['img_id']] ?? '';
            $list[$k]['category_name'] = $category[$v[$CategoryPk]] ?? '(未分类)';
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 内容信息
     * 
     * @param int  $id   内容id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = ContentCache::get($id);
        if (empty($info)) {
            $model = new ContentModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('内容不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $CategoryModel = new CategoryModel();
            $CategoryPk = $CategoryModel->getPk();
            $category = CategoryService::info($info[$CategoryPk], false);
            $info['category_name'] = $category['category_name'] ?? '(未分类)';

            $info['img_url'] = FileService::fileUrl($info['img_id']);
            $info['imgs']    = FileService::fileArray($info['img_ids']);
            $info['files']   = FileService::fileArray($info['file_ids']);
            $info['videos']  = FileService::fileArray($info['video_ids']);

            ContentCache::set($id, $info);
        }

        // 点击量
        $key  = $id . 'hits';
        $hits = ContentCache::get($key);
        if ($hits) {
            $gate = 10;
            if ($hits >= $gate) {
                $model = new ContentModel();
                $pk = $model->getPk();

                $res = $model->where($pk, $id)->inc('hits', $hits)->update();
                if ($res) {
                    ContentCache::del([$key, $id]);
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
     * @param array $param 内容信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

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
     * @param mixed $ids    内容信息id
     * @param array $update 内容信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $update = [])
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        ContentCache::del($ids);

        return $update;
    }

    /**
     * 内容删除
     * 
     * @param mixed $ids  内容id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        ContentCache::del($ids);

        return $update;
    }

    /**
     * 内容上一条
     *
     * @param int $id      内容id
     * @param int $is_cate 是否当前分类
     * 
     * @return array 上一条内容
     */
    public static function prev($id, $is_cate = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $where[] = [$pk, '<', $id];
        $where[] = ['is_delete', '=', 0];
        if ($is_cate) {
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
     * @param int $id      内容id
     * @param int $is_cate 是否当前分类
     * 
     * @return array 下一条内容
     */
    public static function next($id, $is_cate = 0)
    {
        $model = new ContentModel();
        $pk = $model->getPk();

        $where[] = [$pk, '>', $id];
        $where[] = ['is_delete', '=', 0];
        if ($is_cate) {
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
     * 内容统计
     *
     * @return array
     */
    public static function statistics()
    {
        $key = 'count';
        $data = ContentCache::get($key);
        if (empty($data)) {
            $CategoryModel = new CategoryModel();
            $CategoryPk = $CategoryModel->getPk();
            $category = $CategoryModel->field($CategoryPk . ',category_name')->where('is_delete', 0)->select()->toArray();
            $category[] = [$CategoryPk => 0, 'category_name' => '(未分类)'];

            $ContentModel = new ContentModel();
            $count = $ContentModel->where('is_delete', 0)->count();
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
            array_multisort($ss, SORT_DESC, $xs_data);
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
