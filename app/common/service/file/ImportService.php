<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use app\common\cache\file\ImportCache;
use app\common\model\file\ImportModel;

/**
 * 导入文件
 */
class ImportService
{
    /**
     * 类型：会员导入
     */
    public const TYPE_MEMBER = 10;
    /**
     * 类型：会员标签导入
     */
    public const TYPE_MEMBER_TAG = 11;
    /**
     * 类型：内容导入
     */
    public const TYPE_CONTENT = 20;
    /**
     * 类型：文件导入
     */
    public const TYPE_FILE = 30;
    /**
     * 类型
     *
     * @param  integer $type
     * @return string|array
     */
    public static function types($type = '')
    {
        $types = [
            self::TYPE_MEMBER     => '会员导入',
            self::TYPE_MEMBER_TAG => '会员标签导入',
            self::TYPE_CONTENT    => '内容导入',
            self::TYPE_FILE       => '文件导入',
        ];

        if ($type !== '') {
            return $types[$type] ?? '';
        }

        return $types;
    }

    /**
     * 状态：待处理
     */
    public const STATUS_PENDING = 1;
    /**
     * 状态：处理中
     */
    public const STATUS_PROCESSING = 2;
    /**
     * 状态：处理成功
     */
    public const STATUS_SUCCESS = 3;
    /**
     * 状态：处理失败
     */
    public const STATUS_FAIL = 4;
    /**
     * 状态
     *
     * @param  integer $status
     * @return string|array
     */
    public static function statuss($status = '')
    {
        $statuss = [
            self::STATUS_PENDING    => '待处理',
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_SUCCESS    => '处理成功',
            self::STATUS_FAIL       => '处理失败',
        ];

        if ($status !== '') {
            return $statuss[$status] ?? '';
        }

        return $statuss;
    }

    /**
     * 成功文件路径
     * @param string $file_path 文件路径
     * @return string
     */
    public static function filePathSuccess($file_path)
    {
        return substr($file_path, 0, -5) . '-success.xlsx';
    }
    /**
     * 失败文件路径
     * @param string $file_path 文件路径
     * @return string
     */
    public static function filePathFail($file_path)
    {
        return substr($file_path, 0, -5) . '-fail.xlsx';
    }

    /**
     * 修改字段
     * @var array
     */
    public static $edit_field = [
        'import_id/d' => '',
        'remark/s'    => '',
    ];

    /**
     * 导入文件列表
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
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
    {
        $model = new ImportModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',type,file_name,file_path,file_size,status,times,import_num,success_num,fail_num,remark,create_uid,create_time,update_time,delete_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $where = array_values($where);

        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'type')) {
            $append[] = 'type_name';
        }
        if (strpos($field, 'file_name')) {
            $append = array_merge($append, ['file_name_success', 'file_name_fail']);
        }
        if (strpos($field, 'file_path')) {
            $append = array_merge($append, ['file_path_success', 'file_path_fail', 'file_url', 'file_url_success', 'file_url_fail']);
        }
        if (strpos($field, 'file_size')) {
            $append[] = 'file_size';
        }
        if (strpos($field, 'status')) {
            $append[] = 'status_name';
        }
        if (strpos($field, 'create_uid')) {
            $with[] = $hidden[] = 'createUser';
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
        $list = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 导入文件信息
     *
     * @param int  $id   导入id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = ImportCache::get($id);
        if (empty($info)) {
            $model = new ImportModel();
            $pk = $model->getPk();
            $where = [[$pk, '=', $id]];
            $info = $model->with(['createUser'])
                ->append(['type_name', 'file_name_success', 'file_name_fail',  'file_path_success', 'file_path_fail', 'file_url', 'file_url_success', 'file_url_fail', 'file_size', 'status_name'])
                ->where($where)->find()->toArray();
            if (empty($info)) {
                if ($exce) {
                    exception('导入文件不存在：' . $id);
                }
                return [];
            }

            ImportCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 导入文件添加
     *
     * @param array $param 文件信息
     * 
     * @return int|Exception
     */
    public static function add($param)
    {
        $model = new ImportModel();
        $pk = $model->getPk();

        unset($param[$pk]);
        $param['create_time'] = datetime();
        $model->save($param);

        return $model->$pk;
    }

    /**
     * 导入文件修改
     *
     * @param int|array $ids   导入id
     * @param array     $param 文件信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new ImportModel();
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
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        ImportCache::del($ids);

        return $param;
    }

    /**
     * 导入文件删除
     *
     * @param array $ids  导入id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new ImportModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                $file = $model->field('file_path')->where($pk, 'in', $ids)->select();
                foreach ($file as $v) {
                    @unlink($v['file_path']); // 删除文件
                    @unlink($model->getFilePathSuccessAttr(null, $v));
                    @unlink($model->getFilePathFailAttr(null, $v));
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

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        ImportCache::del($ids);

        return $update;
    }
}
