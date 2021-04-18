<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-04-17
 */

namespace app\index\service;

use think\facade\Db;
use app\common\cache\MemberCache;
use app\common\cache\VerifyCache;
use app\common\utils\IpInfoUtils;
use app\common\service\MemberLogService;
use app\common\service\MemberService;

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

        $field = 'member_id,username,nickname,phone,email,avatar,login_num,is_disable';

        $where[] = ['username|phone|email', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('member')
            ->field($field)
            ->where($where)
            ->find();

        if (empty($member)) {
            exception('会员名或密码错误');
        }

        if ($member['is_disable'] == 1) {
            exception('会员已被禁用');
        }

        $ip_info   = IpInfoUtils::info();
        $member_id = $member['member_id'];

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_num']    = $member['login_num'] + 1;
        $update['login_time']   = datetime();
        Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        $member_log['log_type']      = 2;
        $member_log['member_id']     = $member_id;
        $member_log['response_code'] = 200;
        $member_log['response_msg']  = '登录成功';
        MemberLogService::add($member_log);

        MemberCache::del($member_id);
        $member = MemberService::info($member_id);

        VerifyCache::del($param['verify_id']);

        return $member;
    }

    /**
     * 退出
     *
     * @param integer $member_id 会员id
     * 
     * @return array
     */
    public static function logout($member_id)
    {
        $update['logout_time'] = datetime();

        Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        $update['member_id'] = $member_id;

        MemberCache::del($member_id);

        return $update;
    }
}
