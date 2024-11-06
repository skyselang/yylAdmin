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
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'feedback_id/d' => '',
        'member_id'     => 0,
        'receipt_no'    => '',
        'type/d'        => 0,
        'title/s'       => '',
        'content/s'     => '',
        'phone/s'       => '',
        'email/s'       => '',
        'images/a'      => [],
        'reply'         => '',
        'status'        => 0,
        'remark/s'      => '',
    ];

    /**
     * 反馈列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * 
     * @return array ['count', 'pages', 'page', 'limit', 'list']
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new FeedbackModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',member_id,receipt_no,type,title,phone,email,remark,status,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'member_id')) {
            $with[]   = $hidden[] = 'member';
            $append[] = 'member_username';
        }
        if (strpos($field, 'type')) {
            $append[] = 'type_name';
        }
        if (strpos($field, 'status')) {
            $append[] = 'status_name';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $pages = 0;
        if ($total) {
            $count_model = clone $model;
            $count = $count_model->where($where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list  = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->select()->toArray();

        $types   = SettingService::feedbackTypes();
        $statuss = SettingService::feedbackStatuss();

        return compact('count', 'pages', 'page', 'limit', 'list', 'types', 'statuss');
    }

    /**
     * 反馈信息
     * 
     * @param string $id   反馈id、回执编号
     * @param bool   $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = FeedbackCache::get($id);
        if (empty($info)) {
            $model = new FeedbackModel();
            $pk = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['receipt_no', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception('反馈不存在：' . $id);
                }
                return [];
            }
            $info = $info
                ->append(['member_username', 'type_name', 'status_name', 'images'])
                ->hidden(['member', 'image'])
                ->toArray();

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

        if (empty($param['receipt_no'] ?? '')) {
            $param['receipt_no'] = uniqids();
        }
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加图片
            if (isset($param['images'])) {
                $image_ids = file_ids($param['images']);
                $model->image()->saveAll($image_ids);
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

        $receipt_no = $model->where($pk, 'in', $ids)->column('receipt_no');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['images'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改图片
                    if (isset($param['images'])) {
                        $info = $info->append(['image_ids']);
                        relation_update($info, $info['image_ids'], file_ids($param['images']), 'image');
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

        FeedbackCache::del($ids);
        FeedbackCache::del($receipt_no);

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

        $receipt_no = $model->where($pk, 'in', $ids)->column('receipt_no');

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
                    $info->image()->detach();
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
        FeedbackCache::del($receipt_no);

        return $update;
    }
}
