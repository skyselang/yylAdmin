<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\system\SmsLogCache as Cache;
use app\common\model\system\SmsLogModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;

/**
 * 短信日志
 */
class SmsLogService
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
        'log_id/d'    => 0,
        'intcode/s'   => '',
        'phone/s'     => '',
        'template/s'  => '',
        'data',
        'content/s'   => '',
        'create_time' => null,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['intcode', 'phone', 'template', 'content', 'error'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps = $exp ? where_exps() : [];

        return ['exps' => $exps];
    }

    /**
     * 短信日志列表
     * @param array  $where 条件
     * @param int    $page  分页
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="log_id,intcode,phone,template,error,create_time,update_time"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
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
            $field = $pk . ',intcode,phone,template,error,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
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

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 短信日志信息
     * @param int  $id   日志id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="log_id")
     * @Apidoc\Returned(ref={Model::class})
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception(lang('短信日志不存在：') . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 短信日志添加
     * @param array $param 日志数据
     * @Apidoc\Param(ref={Model::class}, withoutField="log_id,is_disable,is_delete,create_uid,update_uid,delete_uid,update_time,delete_time")
     */
    public static function add($param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        $param['create_uid'] = user_id();
        if (empty($param['create_time'] ?? '')) {
            $param['create_time'] = datetime();
        }

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 短信日志修改
     * @param int|array $ids 日志id
     * @param array $param   日志信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,update_time,delete_time")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $param;
    }

    /**
     * 短信日志删除
     * @param array $ids  日志id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $where = [[$pk, 'in', $ids]];
        if ($real) {
            $res = $model->where($where)->delete();
        } else {
            $update = update_softdele();
            $res = $model->where($where)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);

        return $update;
    }

    /**
     * 短信日志批量修改
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
     * 短信日志导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'intcode', 'name' => lang('国际码'), 'width' => 12, 'color' => ''],
            ['field' => 'phone', 'name' => lang('手机'), 'width' => 16],
            ['field' => 'template', 'name' => lang('模板ID'), 'width' => 16],
            ['field' => 'data', 'name' => lang('模板变量'), 'width' => 50],
            ['field' => 'content', 'name' => lang('内容'), 'width' => 50],
            ['field' => 'error', 'name' => lang('错误'), 'width' => 40],
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
     * 短信日志导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_SETTING_SMS_LOG;

        $field = 'intcode,phone,template,create_time,update_time,data,content,error';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 短信日志清空
     * @param array $where 条件
     * @param int   $limit 限制条数，0不限制
     * @Apidoc\Query(ref="searchQuery")
     */
    public static function clear($where = [], $limit = 0)
    {
        $model = self::model();
        $pk    = $model->getPk();

        array_unshift($where, [$pk, '>', 0]);

        if ($limit > 0) {
            $model = $model->limit($limit);
        }
        $count = $model->where($where)->order($pk, 'asc')->delete(true);

        return ['count' => $count];
    }

    /**
     * 短信日志清除
     * @param int $limit 限制条数，0不限制
     */
    public static function clearLog($limit = 10000)
    {
        $setting  = SettingService::info();
        $save_day = $setting['sms_log_save_time'];
        if ($save_day > 0) {
            $cache = self::cache();
            $key   = 'clearLog' . user_id() . '-' . member_id();
            if (empty($cache->get($key))) {
                $where = [['create_time', '<', date('Y-m-d H:i:s', strtotime("-{$save_day} day"))]];
                self::clear($where, $limit);
                $cache->set($key, 1, mt_rand(480, 600));
            }
        }
    }
}
