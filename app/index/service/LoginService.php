<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 登录退出
namespace app\index\service;

use think\facade\Db;
use app\common\cache\MemberCache;
use app\common\utils\IpInfoUtils;
use app\common\service\MemberLogService;
use app\common\service\MemberService;
use app\common\service\TokenService;

class LoginService
{
    /**
     * 登录（账号）
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

        // 通过 账号、手机、邮箱 登录
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

        $ip_info   = IpInfoUtils::info();
        $member_id = $member['member_id'];

        // 登录信息
        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_num']    = $member['login_num'] + 1;
        $update['login_time']   = datetime();
        Db::name('member')
            ->where('member_id', $member_id)
            ->update($update);

        // 登录日志
        $member_log['member_id'] = $member_id;
        MemberLogService::add($member_log, 2);

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::field($member);
        $data['member_token'] = TokenService::create($member);

        return $data;
    }

    /**
     * 微信登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return array
     */
    public static function wechat($userinfo)
    {
        $datetime    = datetime();
        $unionid     = $userinfo['unionid'];
        $openid      = $userinfo['openid'];
        $login_ip    = $userinfo['login_ip'];
        $reg_channel = $userinfo['reg_channel'];
        $ip_info     = IpInfoUtils::info($login_ip);

        foreach ($userinfo as $k => $v) {
            if ($k == 'privilege') {
                $userinfo[$k] = serialize($v);
            }
            if (empty($userinfo[$k])) {
                unset($userinfo[$k]);
            }
        }
        unset($userinfo['login_ip'], $userinfo['reg_channel']);

        // 会员微信信息
        if ($unionid) {
            $wechat_where[] = ['unionid', '=', $unionid];
        } else {
            $wechat_where[] = ['openid', '=', $openid];
        }
        $wechat_where[] = ['is_delete', '=', 0];
        $member_wechat = Db::name('member_wechat')
            ->field('member_wechat_id,member_id')
            ->where($wechat_where)
            ->find();

        // 启动事务
        $errmsg = '';
        Db::startTrans();
        try {
            if ($member_wechat) {
                $member_wechat_id = $member_wechat['member_wechat_id'];
                Db::name('member_wechat')
                    ->where('member_wechat_id', $member_wechat_id)
                    ->update($userinfo);
                $member_id = $member_wechat['member_id'];
            } else {
                $insert_wechat = $userinfo;
                $insert_wechat['create_time'] = $datetime;
                $member_wechat_id = Db::name('member_wechat')
                    ->insertGetId($insert_wechat);
                $member_id = 0;
            }

            $member = Db::name('member')
                ->field('member_id,nickname,login_num')
                ->where(['member_id' => $member_id, 'is_delete' => 0])
                ->find();
            if ($member) {
                if (empty($member['nickname'])) {
                    $member_update['nickname'] = $userinfo['nickname'];
                }
                $member_update['login_num']    = $member['login_num'] + 1;
                $member_update['login_ip']     = $login_ip;
                $member_update['login_time']   = $datetime;
                $member_update['login_region'] = $ip_info['region'];
                Db::name('member')
                    ->where('member_id', $member_id)
                    ->update($member_update);
                // 登录日志
                $member_log['member_id'] = $member_id;
                MemberLogService::add($member_log, 2);
            } else {
                if ($reg_channel == 2) {
                    $member_insert['username'] = 'wechatOffi' . $member_wechat_id;
                } elseif ($reg_channel == 3) {
                    $member_insert['username'] = 'wechatMini' . $member_wechat_id;
                } else {
                    $member_insert['username'] = 'wechat' . $member_wechat_id;
                }
                $member_insert['login_num']    = 1;
                $member_insert['login_ip']     = $login_ip;
                $member_insert['login_time']   = $datetime;
                $member_insert['login_region'] = $ip_info['region'];
                $member_insert['create_time']  = $datetime;
                $member_insert['reg_channel']  = $reg_channel;
                $member_insert['nickname']     = $userinfo['nickname'];
                $member_insert['password']     = '';
                $member_id = Db::name('member')
                    ->insertGetId($member_insert);
                // 注册日志
                $member_log['member_id'] = $member_id;
                MemberLogService::add($member_log, 1);
            }

            $wechat_update = $userinfo;
            $wechat_update['member_id']   = $member_id;
            $wechat_update['update_time'] = $datetime;
            Db::name('member_wechat')
                ->where('member_wechat_id', $member_wechat_id)
                ->update($wechat_update);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            $errmsg = '微信登录失败:' . $e->getMessage();
            // 回滚事务
            Db::rollback();
        }

        if ($errmsg) {
            exception($errmsg);
        }

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::field($member);
        $data['member_token'] = TokenService::create($member);

        return $data;
    }

    /**
     * 登录返回字段
     *
     * @param array $member 会员信息
     *
     * @return array
     */
    public static function field($member)
    {
        $data = [];
        $field = ['member_id', 'username', 'nickname', 'phone', 'email', 'login_ip', 'login_time', 'login_num', 'avatar_url'];
        foreach ($field as $k => $v) {
            $data[$v] = $member[$v];
        }

        return $data;
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

        MemberCache::del($member_id);

        $update['member_id'] = $member_id;

        return $update;
    }
}
