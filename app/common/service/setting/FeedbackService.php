<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\FeedbackCache;
use app\common\model\setting\FeedbackModel;

/**
 * 反馈管理
 */
class FeedbackService
{
    /**
     * 反馈列表
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
        $model = new FeedbackModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',type,title,phone,email,remark,is_unread,create_time,update_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list  = $model->field($field)->where($where)
            ->append(['type_name'])
            ->page($page)->limit($limit)->order($order)->select()->toArray();

        $types = SettingService::feedback_types();

        return compact('count', 'pages', 'page', 'limit', 'list', 'types');
    }

    /**
     * 反馈信息
     * 
     * @param int  $id   反馈id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = FeedbackCache::get($id);
        if (empty($info)) {
            $model = new FeedbackModel();

            $info = $model->with(['images'])->append(['type_name'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('反馈不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            if ($info['is_unread']) {
                $update['is_unread']  = 0;
                $update['update_uid'] = user_id();
                $update['read_time']  = $info['read_time'] = datetime();
                self::edit($id, $update);
            }

            FeedbackCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 反馈添加
     *
     * @param array $param 反馈信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new FeedbackModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加图片
            $image_ids = file_ids($param['images']);
            $model->images()->saveAll($image_ids);
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
     * 反馈修改 
     *     
     * @param int|array $ids   反馈id
     * @param array     $param 反馈信息
     *     
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new FeedbackModel();
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
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (isset($param['images'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改图片
                    $info->images()->detach();
                    $image_ids = file_ids($param['images']);
                    $info->images()->saveAll($image_ids);
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

        FeedbackCache::del($ids);

        return $param;
    }

    /**
     * 反馈删除
     * 
     * @param int|array $ids  反馈id
     * @param bool      $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new FeedbackModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除图片
                    $info->images()->detach();
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

        FeedbackCache::del($ids);

        return $update;
    }
}
