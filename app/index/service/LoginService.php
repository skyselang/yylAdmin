<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-12-07
 */

namespace app\index\service;

use think\facade\Db;
use app\common\cache\MemberCache;
use app\common\cache\VerifyCache;
use app\common\service\IpInfoService;
use app\admin\service\LogService;
use app\admin\service\ApiService;
use app\admin\service\MemberService;

class LoginService
{
    /**
     * 登录
     *
     * @param array $param 登录信息
     * 
     * @return array
     */
    public static function login($param)
    {
        $username = $param['username'];
        $password = md5($param['password']);

        $field = 'member_id,username,nickname,phone,email,login_num,is_disable';

        $where[] = ['username|phone|email', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('member')
            ->field($field)
            ->where($where)
            ->find();

        if (empty($member)) {
            exception('账号或密码错误');
        }

        if ($member['is_disable'] == 1) {
            exception('账号已被禁用');
        }

        $request_ip = $param['request_ip'];
        $ipinfo     = IpInfoService::info($request_ip);

        $member_id = $member['member_id'];

        $update['login_ip']     = $request_ip;
        $update['login_region'] = $ipinfo['region'];
        $update['login_time']   = date('Y-m-d H:i:s');
        $update['login_num']    = $member['login_num'] + 1;
        Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        MemberCache::del($member_id);

        $api_url = request_pathinfo();
        $api     = ApiService::info($api_url);

        $request_param['username'] = $username;
        if ($param['verify_id']) {
            $request_param['verify_id']   = $param['verify_id'];
            $request_param['verify_code'] = $param['verify_code'];
        }

        $log['log_type']       = 1;
        $log['member_id']      = $member_id;
        $log['api_id']         = $api['api_id'];
        $log['request_ip']     = $request_ip;
        $log['request_method'] = $param['request_method'];
        $log['request_param']  = serialize($request_param);
        LogService::add($log);

        VerifyCache::del($param['verify_id']);

        $member = MemberService::info($member_id);

        return $member;
    }

    /**
     * 退出
     *
     * @param integer $member_id 用户id
     * 
     * @return array
     */
    public static function logout($member_id)
    {
        $update['logout_time'] = date('Y-m-d H:i:s');

        Db::name('member')->where('member_id', $member_id)->update($update);

        MemberCache::del($member_id);

        $update['member_id'] = $member_id;

        return $update;
    }
}
