<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\CarouselCache;
use app\common\model\setting\CarouselModel;

/**
 * 轮播管理
 */
class CarouselService
{
    /**
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
        'carousel_id/d' => 0,
        'unique/s'      => '',
        'file_id/d'     => 0,
        'title/s'       => '',
        'link/s'        => '',
        'desc/s'        => '',
        'sort/d'        => 250,
        'file_list/a'   => [],
    ];

    /**
     * 轮播列表
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
        $model = new CarouselModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',unique,file_id,title,link,position,desc,sort,is_disable,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->with(['file'])
            ->append(['file_url', 'file_name', 'file_ext', 'file_type', 'file_type_name'])
            ->hidden(['file'])
            ->page($page)->limit($limit)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 轮播信息
     * 
     * @param int|string $id   轮播id
     * @param bool       $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = CarouselCache::get($id);
        if (empty($info)) {
            $model = new CarouselModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('轮播不存在：' . $id);
                }
                return [];
            }
            $info = $info->append(['file_url', 'file_name', 'file_ext', 'file_type', 'file_type_name', 'file_list'])->toArray();

            CarouselCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 轮播添加
     *
     * @param array $param 轮播信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new CarouselModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加文件列表
            $file_list = $param['file_list'] ?? [];
            if ($file_list) {
                $file_list_ids = file_ids($file_list);
                $model->files()->saveAll($file_list_ids);
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

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 轮播修改
     *     
     * @param int|array $ids   轮播id
     * @param array     $param 轮播信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new CarouselModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $unique = $model->where($pk, 'in', $ids)->column('unique');
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (isset($param['file_list'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改文件列表
                    if (isset($param['file_list'])) {
                        $info->files()->detach();
                        $file_list_ids = file_ids($param['file_list']);
                        $info->files()->saveAll($file_list_ids);
                    }
                }
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

        $param['ids'] = $ids;

        CarouselCache::del($ids);
        CarouselCache::del($unique);

        return $param;
    }

    /**
     * 轮播删除
     * 
     * @param array $ids  轮播id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new CarouselModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $unique = $model->where($pk, 'in', $ids)->column('unique');
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除文件列表
                    $info->files()->detach();
                }
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
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

        CarouselCache::del($ids);
        CarouselCache::del($unique);

        return $update;
    }
}
