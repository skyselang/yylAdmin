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
use app\common\service\utils\IpInfoUtils;

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
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        $model = new LogModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',member_id,api_id,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->with(['member', 'api'])
            ->append(['nickname', 'username', 'api_url', 'api_name'])
            ->hidden(['member', 'api'])
            ->page($page)->limit($limit)->order($order)->select()->toArray();

        $log_types = SettingService::log_types();

        return compact('count', 'pages', 'page', 'limit', 'list', 'log_types');
    }

    /**
     * 会员日志信息
     *
     * @param int  $id   日志id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
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
            $info = $info->append(['nickname', 'username', 'api_url', 'api_name'])->toArray();

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

            $api     = ApiService::info('', false);
            $ip_info = IpInfoUtils::info();

            $param['api_id']           = $api['api_id'] ?? 0;
            $param['log_type']         = $log_type;
            $param['request_ip']       = $ip_info['ip'];
            $param['request_country']  = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city']     = $ip_info['city'];
            $param['request_area']     = $ip_info['area'];
            $param['request_region']   = $ip_info['region'];
            $param['request_isp']      = $ip_info['isp'];
            $param['request_param']    = $request_param;
            $param['request_method']   = Request::method();
            $param['create_time']      = datetime();
            $param['user_agent']       = $_SERVER['HTTP_USER_AGENT'] ?? Request::header('user-agent') ?? '';

            $model = new LogModel();
            $model->save($param);
        }
    }

    /**
     * 会员日志修改
     *
     * @param array $param 日志信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new LogModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_uid']  = user_id();
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        LogCache::del($ids);

        return $update;
    }

    /**
     * 会员日志删除
     *
     * @param mixed $ids  日志id
     * @param bool  $real 是否真实删除
     * 
     * @return array
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
     * 
     * @return array
     */
    public static function clear($where = [])
    {
        $model = new LogModel();
        $pk = $model->getPk();

        if (empty($where)) {
            $where[] = [$pk, '>', 0];
        }

        $count = $model->where($where)->delete(true);

        $data['count'] = $count;

        return $data;
    }
}
