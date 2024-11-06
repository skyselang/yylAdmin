<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Config;
use think\facade\Request;
use app\common\cache\member\LogCache;
use app\common\model\member\LogModel;
use app\common\service\utils\Utils;

/**
 * 会员日志
 */
class LogService
{
    /**
     * 会员日志列表
     *
     * @param array  $where 条件
     * @param int    $page  分页
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * 
     * @return array ['count', 'pages', 'page', 'limit', 'list']
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',member_id,api_id,request_ip,request_region,request_isp,request_url,response_code,response_msg,application,create_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'member_id')) {
            $with[] = $hidden[] = 'member';
            $append = array_merge($append, ['nickname', 'username']);
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
     * 会员日志信息
     *
     * @param int  $id   日志id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = LogCache::get($id);
        if (empty($info)) {
            $model = new LogModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('会员日志不存在：' . $id);
                }
                return [];
            }
            $info = $info
                ->append(['nickname', 'username', 'api_url', 'api_name', 'platform_name', 'application_name'])
                ->hidden(['member', 'api'])
                ->toArray();

            LogCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员日志添加
     *
     * @param array $param    日志信息
     * @param int   $log_type 日志类型：0注册，1登录，2操作，3退出
     * 
     * @return void
     */
    public static function add($param = [], $log_type = SettingService::LOG_TYPE_OPERATION)
    {
        // 会员日记记录是否开启
        if (member_log_switch()) {
            if ($log_type == SettingService::LOG_TYPE_REGISTER) {
                $param['response_code'] = 200;
                $param['response_msg']  = '注册成功';
            } elseif ($log_type == SettingService::LOG_TYPE_LOGIN) {
                $param['response_code'] = 200;
                $param['response_msg']  = '登录成功';
            }
            if (($param['response_msg'] ?? '') == '退出成功') {
                $log_type = SettingService::LOG_TYPE_LOGOUT;
            }

            // 请求参数排除字段
            $request_param = Request::param();
            $param_without = Config::get('api.log_param_without', []);
            array_push($param_without, Config::get('api.token_name'));
            foreach ($param_without as $v) {
                unset($request_param[$v]);
            }

            $api        = ApiService::info('', false);
            $ip_info    = Utils::ipInfo();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? Request::header('user-agent') ?? '';

            $param['api_id']           = $api['api_id'] ?? 0;
            $param['log_type']         = $log_type;
            $param['request_url']      = $api['api_url'] ?? Request::baseUrl();
            $param['request_method']   = Request::method();
            $param['request_ip']       = $ip_info['ip'];
            $param['request_country']  = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city']     = $ip_info['city'];
            $param['request_area']     = $ip_info['area'];
            $param['request_region']   = $ip_info['region'];
            $param['request_isp']      = $ip_info['isp'];
            $param['request_param']    = $request_param;
            $param['create_time']      = datetime();
            $param['user_agent']       = substr($user_agent, 0, 1024);

            $model = new LogModel();
            $model->save($param);
        }
    }

    /**
     * 会员日志修改
     *
     * @param int|array $ids   日志id
     * @param array     $param 日志信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new LogModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        if (isset($param['request_param'])) {
            $param['request_param'] = serialize($param['request_param']);
        }

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        LogCache::del($ids);

        return $param;
    }

    /**
     * 会员日志删除
     *
     * @param mixed $ids  日志id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = delete_update();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        LogCache::del($ids);

        return $update;
    }

    /**
     * 会员日志清空
     * 
     * @param array $where 条件
     * @param int $limit 限制条数，0不限制
     * 
     * @return array
     */
    public static function clear($where = [], $limit = 0)
    {
        $model = new LogModel();
        $pk = $model->getPk();

        $where[] = [$pk, '>', 0];

        if ($limit > 0) {
            $model = $model->limit($limit);
        }
        $count = $model->where($where)->delete(true);

        return compact('count');
    }

    /**
     * 会员日志清除
     * 
     * @return void
     */
    public static function clearLog()
    {
        $setting = SettingService::info('log_save_time');
        $days = $setting['log_save_time'];
        if ($days > 0 && 0 <= date('H') && date('H') <= 8) {
            $key = 'clear';
            if (empty(LogCache::get($key))) {
                $where = [['create_time', '<', date('Y-m-d H:i:s', strtotime("-{$days} day"))]];
                LogService::clear($where, 10000);
                LogCache::set($key, 1, 600);
            }
        }
    }
}
