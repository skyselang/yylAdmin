<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\member\LogCache as Cache;
use app\common\model\member\LogModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\system\SettingService as SystemSettingService;
use app\common\service\file\ExportService;
use app\common\utils\Utils;

/**
 * 会员日志
 */
class LogService
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
        'log_id/d'         => 0,
        'member_id/d'      => 0,
        'api_id/d'         => 0,
        'request_method/s' => '',
        'request_ip/s'     => '',
        'request_isp/s'    => '',
        'request_region/s' => '',
        'request_param',
        'response_code/s'  => '',
        'response_msg/s'   => '',
        'user_agent/s'     => '',
        'application/d'    => 0,
        'is_disable/d'     => 0,
        'create_time'      => null,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = [];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("log_types", type="array", desc="类型"),
     *   @Apidoc\Returned("platforms", type="array", desc="平台"),
     *   @Apidoc\Returned("applications", type="array", desc="应用"),
     *   @Apidoc\Returned("apis", ref={ApiService::class,"info"}, type="tree", desc="接口树形", field="api_id,api_pid,api_name"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps         = $exp ? where_exps() : [];
        $log_types    = SettingService::logTypes('', true);
        $methods      = SystemSettingService::methods(true);
        $platforms    = SettingService::platforms('', true);
        $applications = SettingService::applications('', true);
        $apis         = ApiService::list('tree', [where_delete()], [], 'api_name');

        return [
            'exps'         => $exps,
            'log_types'    => $log_types,
            'methods'      => $methods,
            'platforms'    => $platforms,
            'applications' => $applications,
            'apis'         => $apis,
        ];
    }

    /**
     * 会员日志列表
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
     *   @Apidoc\Returned(ref={Model::class}, field="log_id,member_id,api_id,api_name,request_ip,request_region,request_isp,request_url,response_code,response_msg,application,create_time"),
     *   @Apidoc\Returned(ref={Model::class,"member"}, field="member_nickname,member_username"),
     *   @Apidoc\Returned(ref={Model::class,"api"}, field="api_name,api_url"),
     *   @Apidoc\Returned(ref={Model::class,"getPlatformNameAttr"}, field="platform_name"),
     *   @Apidoc\Returned(ref={Model::class,"getApplicationNameAttr"}, field="application_name"),
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
            $field = $pk . ',member_id,api_id,api_name,request_ip,request_region,request_isp,request_url,response_code,response_msg,application,create_time';
        } else {
            $field = $pk . ',' . $field;
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'member_id')) {
            $with[] = $hidden[] = 'member';
            $append = array_merge($append, ['member_nickname', 'member_username']);
        }
        if (strpos($field, 'api_id')) {
            $with[] = $hidden[] = 'api';
            $append = array_merge($append, ['api_name', 'api_url']);
        }
        if (strpos($field, 'platform')) {
            $append[] = 'platform_name';
        }
        if (strpos($field, 'application')) {
            $append[] = 'application_name';
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

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 会员日志信息
     * @param int  $id   日志id
     * @param bool $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="log_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"member"}, field="member_nickname,member_username")
     * @Apidoc\Returned(ref={Model::class,"api"}, field="api_name,api_url")
     * @Apidoc\Returned(ref={Model::class,"getPlatformNameAttr"}, field="platform_name")
     * @Apidoc\Returned(ref={Model::class,"getApplicationNameAttr"}, field="application_name")
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
                    exception(lang('会员日志不存在：') . $id);
                }
                return [];
            }
            $info = $info
                ->append(['member_nickname', 'member_username', 'api_url', 'api_name', 'platform_name', 'application_name'])
                ->hidden(['member', 'api'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 会员日志添加
     * @param array $param 日志信息
     * @param bool  $auto  自动记录
     * @Apidoc\Param(ref={Model::class}, withoutField="log_id,is_disable,is_delete,create_uid,update_uid,delete_uid,update_time,delete_time")
     */
    public static function add($param = [], $auto = true)
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk]);
        if ($auto) {
            $api_info = ApiService::info('', false);
            $ip_info  = Utils::ipInfo();
            $setting  = SettingService::info();

            $request_param = request()->param();
            $param_without = explode(',', $setting['log_param_without'] ?? '');
            $param_without = array_merge($param_without, config('api.log_param_without', []));
            $request_param = request_param_exclude($request_param, $param_without);

            if (empty($param['member_id'] ?? '')) {
                $log_type = $api_info['log_type'] ?? '';
                if ($log_type === SettingService::LOG_TYPE_LOGIN || $log_type === SettingService::LOG_TYPE_REGISTER) {
                    $param['member_id'] = $param['response_data']['data']['member_id'] ?? 0;
                }
            }
            unset($param['response_data']);

            $param['api_id']         = $api_info['api_id'] ?? 0;
            $param['request_url']    = $api_info['api_url'] ?? request()->baseUrl();
            $param['request_method'] = request()->method();
            $param['request_param']  = $request_param;
            $param['request_region'] = $ip_info['region'];
            $param['request_isp']    = $ip_info['isp'];
            $param['user_agent']     = $_SERVER['HTTP_USER_AGENT'] ?? request()->header('user-agent') ?? '';
            $param['create_time']    = datetime();
        } else {
            $api_info = ApiService::info($param['api_id'] ?? 0, false);
            $ip_info  = Utils::ipInfo(empty($param['request_ip'] ?? '') ? null : $param['request_ip']);
            $param['request_url']    = $api_info['api_url'] ?? '';
            $param['request_region'] = empty($param['request_region']) ? $ip_info['region'] : $param['request_region'];
            $param['request_isp']    = empty($param['request_isp']) ? $ip_info['isp'] : $param['request_isp'];
            $param['create_time']    = empty($param['create_time']) ? NULL : $param['create_time'];
        }

        $param['log_type']         = $api_info['log_type'] ?? SettingService::LOG_TYPE_OPERATION;
        $param['api_name']         = $api_info['api_name'] ?? '';
        $param['request_ip']       = $ip_info['ip'];
        $param['request_country']  = $ip_info['country'];
        $param['request_province'] = $ip_info['province'];
        $param['request_city']     = $ip_info['city'];
        $param['request_area']     = $ip_info['area'];
        $param['response_msg']     = mb_substr($param['response_msg'] ?? '', 0, 1024);
        $param['user_agent']       = mb_substr($param['user_agent'] ?? '', 0, 1024);
        $param['platform']         = SettingService::platform($param['application'] ?? 0);

        $model->save($param);

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 会员日志修改
     * @param int|array $ids   日志id
     * @param array     $param 日志信息
     * @Apidoc\Param(ref={Model::class}, withoutField="is_disable,is_delete,create_uid,update_uid,delete_uid,update_time,delete_time")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $api_info = ApiService::info($param['api_id'] ?? 0, false);
        $ip_info  = Utils::ipInfo(empty($param['request_ip'] ?? '') ? null : $param['request_ip']);
        $param['log_type']         = $api_info['log_type'] ?? SettingService::LOG_TYPE_OPERATION;
        $param['request_url']      = $api_info['api_url'] ?? '';
        $param['request_region']   = empty($param['request_region']) ? $ip_info['region'] : $param['request_region'];
        $param['request_isp']      = empty($param['request_isp']) ? $ip_info['isp'] : $param['request_isp'];
        $param['request_ip']       = $ip_info['ip'];
        $param['request_country']  = $ip_info['country'];
        $param['request_province'] = $ip_info['province'];
        $param['request_city']     = $ip_info['city'];
        $param['request_area']     = $ip_info['area'];
        $param['user_agent']       = substr($param['user_agent'] ?? '', 0, 1024);
        $param['platform']         = SettingService::platform($param['application'] ?? 0);
        $param['create_time']      = empty($param['create_time']) ? NULL : $param['create_time'];
        $param['update_uid']       = user_id();
        $param['update_time']      = datetime();

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
     * 会员日志删除
     * @param mixed $ids  日志id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = update_softdele();
            $res = $model->where($pk, 'in', $ids)->update($update);
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
     * 会员日志是否禁用
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
     * 会员日志批量修改
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
     * 会员日志导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 12],
            ['field' => 'member_id', 'name' => lang('会员ID'), 'width' => 12],
            ['field' => 'member_nickname', 'name' => lang('会员昵称'), 'width' => 28, 'color' => ''],
            ['field' => 'member_username', 'name' => lang('会员用户名'), 'width' => 28],
            ['field' => 'api_id', 'name' => lang('接口ID'), 'width' => 12],
            ['field' => 'api_name', 'name' => lang('接口名称'), 'width' => 20],
            ['field' => 'api_url', 'name' => lang('接口链接'), 'width' => 30],
            ['field' => 'request_ip', 'name' => lang('请求IP'), 'width' => 10],
            ['field' => 'request_region', 'name' => lang('请求地区'), 'width' => 20],
            ['field' => 'request_isp', 'name' => lang('请求ISP'), 'width' => 12],
            ['field' => 'response_code', 'name' => lang('返回码'), 'width' => 12],
            ['field' => 'response_msg', 'name' => lang('返回描述'), 'width' => 30],
            ['field' => 'application_name', 'name' => lang('应用'), 'width' => 10],
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
     * 会员日志导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_MEMBER_LOG;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 会员日志清空
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

        return compact('count');
    }

    /**
     * 会员日志清除
     * @param int $limit 限制条数，0不限制
     */
    public static function clearLog($limit = 10000)
    {
        $setting  = SettingService::info('log_save_time');
        $save_day = $setting['log_save_time'];
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
