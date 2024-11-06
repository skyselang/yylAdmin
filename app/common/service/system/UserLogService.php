<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use think\facade\Config;
use think\facade\Request;
use app\common\cache\system\UserLogCache;
use app\common\model\system\UserLogModel;
use app\common\service\utils\Utils;

/**
 * 用户日志
 */
class UserLogService
{
    /**
     * 用户日志列表
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
        $model = new UserLogModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',user_id,menu_id,request_url,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        if (user_hide_where()) {
            $where[] = user_hide_where();
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'user_id')) {
            $with[] = $hidden[] = 'user';
            $append = array_merge($append, ['nickname', 'username']);
        }
        if (strpos($field, 'menu_id')) {
            $with[] = $hidden[] = 'menu';
            $append = array_merge($append, ['menu_name', 'menu_url']);
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
     * 用户日志信息
     *
     * @param int  $id   日志id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = UserLogCache::get($id);
        if (empty($info)) {
            $model = new UserLogModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('用户日志不存在：' . $id);
                }
                return [];
            }
            $info = $info
                ->append(['nickname', 'username', 'menu_name', 'menu_url'])
                ->hidden(['user', 'menu'])
                ->toArray();

            UserLogCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 用户日志添加
     *
     * @param array $param    日志数据
     * @param int   $log_type 日志类型：0登录，1操作，2退出
     * 
     * @return void
     */
    public static function add($param = [], $log_type = SettingService::LOG_TYPE_OPERATION)
    {
        // 用户日志记录是否开启
        if (user_log_switch()) {
            if ($log_type == SettingService::LOG_TYPE_LOGIN) {
                $param['response_code'] = 200;
                $param['response_msg']  = '登录成功';
            }
            if (($param['response_msg'] ?? '') == '退出成功') {
                $log_type = SettingService::LOG_TYPE_LOGOUT;
            }

            // 请求参数排除字段
            $request_param = Request::param();
            $param_without = Config::get('admin.log_param_without', []);
            array_push($param_without, Config::get('admin.token_name'));
            foreach ($param_without as $v) {
                unset($request_param[$v]);
            }

            $menu       = MenuService::info('', false);
            $ip_info    = Utils::ipInfo();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? Request::header('user-agent') ?? '';

            $param['menu_id']          = $menu['menu_id'] ?? 0;
            $param['log_type']         = $log_type;
            $param['request_method']   = Request::method();
            $param['request_url']      = $menu['menu_url'] ?? Request::baseUrl();
            $param['request_ip']       = $ip_info['ip'];
            $param['request_country']  = $ip_info['country'];
            $param['request_province'] = $ip_info['province'];
            $param['request_city']     = $ip_info['city'];
            $param['request_area']     = $ip_info['area'];
            $param['request_region']   = $ip_info['region'];
            $param['request_isp']      = $ip_info['isp'];
            $param['request_param']    = $request_param;
            $param['user_agent']       = substr($user_agent, 0, 1024);
            $param['create_time']      = datetime();

            $model = new UserLogModel();
            $model->save($param);
        }
    }

    /**
     * 用户日志修改
     *
     * @param array $ids   用户id
     * @param array $param 日志信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new UserLogModel();
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

        UserLogCache::del($ids);

        return $param;
    }

    /**
     * 用户日志删除
     *
     * @param array  $ids     日志id
     * @param bool   $real    是否真实删除
     * @param string $user_id 用户ID
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false, $user_id = '')
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $where = [[$pk, 'in', $ids]];
        if ($user_id !== '') {
            $where[] = ['user_id', 'in', $user_id];
        }
        if ($real) {
            $res = $model->where($where)->delete();
        } else {
            $update = delete_update();
            $res = $model->where($where)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        UserLogCache::del($ids);

        return $update;
    }

    /**
     * 用户日志清空
     * 
     * @param array $where 条件
     * @param int $limit 限制条数，0不限制
     * 
     * @return array
     */
    public static function clear($where = [], $limit = 0)
    {
        $model = new UserLogModel();
        $pk = $model->getPk();

        $where[] = [$pk, '>', 0];

        if ($limit > 0) {
            $model = $model->limit($limit);
        }
        $count = $model->where($where)->delete(true);

        return compact('count');
    }

    /**
     * 用户日志清除
     * 
     * @return void
     */
    public static function clearLog()
    {
        $setting = SettingService::info('log_save_time');
        $days = $setting['log_save_time'];
        if ($days > 0 && 0 <= date('H') && date('H') <= 8) {
            $key = 'clear';
            if (empty(UserLogCache::get($key))) {
                $where = [['create_time', '<', date('Y-m-d H:i:s', strtotime("-{$days} day"))]];
                UserLogService::clear($where, 10000);
                UserLogCache::set($key, 1, 600);
            }
        }
    }
}
