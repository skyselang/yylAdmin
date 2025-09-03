<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\setting\FeedbackCache as Cache;
use app\common\model\setting\FeedbackModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;

/**
 * 反馈管理
 */
class FeedbackService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 添加修改字段
     */
    public static $editField = [
        'feedback_id' => '',
        'member_id'   => 0,
        'unique'      => '',
        'type/d'      => 0,
        'title/s'     => '',
        'content/s'   => '',
        'phone/s'     => '',
        'email/s'     => '',
        'images/a'    => [],
        'reply'       => '',
        'status'      => 0,
        'remark/s'    => '',
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['remark', 'sort', 'unique', 'type', 'status'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("types", type="array", desc="类型"),
     *   @Apidoc\Returned("statuss", type="array", desc="状态"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps   = $exp ? where_exps() : [];
        $types  = SettingService::feedbackTypes('', true);
        $status = SettingService::feedbackStatuss('', true);

        return ['exps' => $exps, 'types' => $types, 'statuss' => $status];
    }

    /**
     * 反馈列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="feedback_id,member_id,unique,type,title,phone,email,remark,status,is_disable,create_time,update_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getMemberNicknameAttr"}, field="member_nickname"),
     *   @Apidoc\Returned(ref={Model::class,"getMemberUsernameAttr"}, field="member_username"),
     *   @Apidoc\Returned(ref={Model::class,"getTypeNameAttr"}, field="type_name"),
     *   @Apidoc\Returned(ref={Model::class,"getStatusNameAttr"}, field="status_name"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }
        if (empty($field)) {
            $field = $pk . ',member_id,unique,type,title,status,sort,remark,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'member_id')) {
            $with[]   = $hidden[] = 'member';
            $append[] = 'member_nickname';
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
            $count = model_where($model->clone(), $where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 反馈信息
     * @param string $id   反馈id、回执编号
     * @param bool   $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="feedback_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getMemberNicknameAttr"}, field="member_nickname")
     * @Apidoc\Returned(ref={Model::class,"getMemberUsernameAttr"}, field="member_username")
     * @Apidoc\Returned(ref={Model::class,"getTypeNameAttr"}, field="type_name")
     * @Apidoc\Returned(ref={Model::class,"getStatusNameAttr"}, field="status_name")
     * @Apidoc\Returned(ref="imagesReturn")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('反馈不存在：') . $id);
                }
                return [];
            }
            $info = $info
                ->append(['member_nickname', 'member_username', 'type_name', 'status_name', 'images', 'is_disable_name'])
                ->hidden(['member', 'image'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 反馈添加
     * @param array $param 反馈信息
     * @Apidoc\Param(ref={Model::class}, field="member_id,unique,type,title,phone,email,content,reply,remark")
     * @Apidoc\Param(ref="imagesParam")
     */
    public static function add($param)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if (empty($param['unique'] ?? '')) {
            $param['unique'] = uniqids();
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
     * @param int|array $ids   反馈id
     * @param array     $param 反馈信息
     * @Apidoc\Param(ref={Model::class}, field="feedback_id,member_id,unique,type,title,phone,email,content,reply,remark")
     * @Apidoc\Param(ref="imagesParam")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

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
                        model_relation_update($info, $info['image_ids'], file_ids($param['images']), 'image');
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

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $param;
    }

    /**
     * 反馈删除
     * @param int|array $ids  反馈id
     * @param bool      $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

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
                $update = update_softdele();
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

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $update;
    }

    /**
     * 反馈是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        return $data;
    }

    /**
     * 反馈批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }

    /**
     * 反馈导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $type       = $exp_imp == 'export' ? 'type_name' : 'type';
        $status     = $exp_imp == 'export' ? 'status_name' : 'status';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'unique', 'name' => lang('编号'), 'width' => 20],
            ['field' => $type, 'name' => lang('类型'), 'width' => 12],
            ['field' => 'member_id', 'name' => lang('会员ID'), 'width' => 12, 'color' => ''],
            ['field' => 'member_nickname', 'name' => lang('会员昵称'), 'width' => 22],
            ['field' => 'member_username', 'name' => lang('会员用户名'), 'width' => 18],
            ['field' => 'title', 'name' => lang('标题'), 'width' => 30],
            ['field' => 'content', 'name' => lang('内容'), 'width' => 40],
            ['field' => 'phone', 'name' => lang('手机'), 'width' => 14],
            ['field' => 'email', 'name' => lang('邮箱'), 'width' => 30],
            ['field' => $status, 'name' => lang('状态'), 'width' => 10],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22],
        ];
        // 生成下标
        foreach ($header as $index => &$value) {
            $value['index'] = $index;
        }
        if ($exp_imp == 'import') {
            $header[] = ['index' => -1, 'field' => 'result_msg', 'name' => lang('导入结果'), 'width' => 60];
        }

        return $header;
    }

    /**
     * 反馈导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SETTING_FEEDBACK;

        $field = 'member_id,unique,type,title,content,phone,email,status,sort,remark,is_disable,create_time,update_time';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }
}
