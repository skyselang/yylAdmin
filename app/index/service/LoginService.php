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
        $data = self::loginField($member);
        $data['member_token'] = TokenService::create($member);

        return $data;
    }

    /**
     * 公众号登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return array
     */
    public static function offiLogin($userinfo)
    {
        $unionid  = $userinfo['unionid'];
        $openid   = $userinfo['openid'];
        $login_ip = $userinfo['login_ip'];

        unset($userinfo['login_ip']);

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
        $res = false;
        $msg = '微信登录失败，请重试';
        Db::startTrans();
        try {
            $datetime = datetime();
            $ip_info  = IpInfoUtils::info($login_ip);

            $insert['login_num']    = 1;
            $insert['login_ip']     = $login_ip;
            $insert['login_time']   = $datetime;
            $insert['login_region'] = $ip_info['region'];
            $insert['create_time']  = $datetime;

            // 已注册
            if ($member_wechat) {
                $member_id = $member_wechat['member_id'];
                $member_wechat_id = $member_wechat['member_wechat_id'];
                $username = 'wechat_offi_' . $member_wechat_id;

                if ($member_id) {
                    $member = Db::name('member')
                        ->field('member_id,login_num')
                        ->where(['member_id' => $member_id, 'is_delete' => 0])
                        ->find();

                    if ($member) {
                        $update['login_num']    = $member['login_num'] + 1;
                        $update['login_ip']     = $login_ip;
                        $update['login_time']   = $datetime;
                        $update['login_region'] = $ip_info['region'];
                        Db::name('member')
                            ->where('member_id', $member_id)
                            ->update($update);
                    } else {
                        $insert['username'] = $username;
                        $insert['password'] = '';
                        $member_id = Db::name('member')
                            ->insertGetId($insert);
                    }
                } else {
                    $insert['username'] = $username;
                    $insert['password'] = '';
                    $member_id = Db::name('member')
                        ->insertGetId($insert);
                }

                $update_wechat = $userinfo;
                $update_wechat['member_id'] = $member_id;
                $update_wechat['update_time'] = $datetime;
                Db::name('member_wechat')
                    ->where('member_wechat_id', $member_wechat_id)
                    ->update($update_wechat);
            } else {
                // 未注册
                $insert_wechat = $userinfo;
                $insert_wechat['create_time'] = $datetime;
                $member_wechat_id = Db::name('member_wechat')
                    ->insertGetId($insert_wechat);

                $insert['reg_channel'] = 2;
                $insert['username']    = 'wechat_offi_' . $member_wechat_id;
                $insert['password']    = '';
                $member_id = Db::name('member')
                    ->insertGetId($insert);

                Db::name('member_wechat')
                    ->where('member_wechat_id', $member_wechat_id)
                    ->update(['member_id' => $member_id]);
            }

            $res = true;
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            // 回滚事务
            Db::rollback();
        }

        if (empty($res)) {
            exception($msg);
        }

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::loginField($member);
        $data['member_token'] = TokenService::create($member);

        return $data;
    }

    /**
     * 小程序登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return array
     */
    public static function miniLogin($userinfo)
    {
        $unionid  = $userinfo['unionid'];
        $openid   = $userinfo['openid'];
        $login_ip = $userinfo['login_ip'];

        unset($userinfo['login_ip']);

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
        $res = false;
        $msg = '微信登录失败，请重试';
        Db::startTrans();
        try {
            $datetime = datetime();
            $ip_info  = IpInfoUtils::info($login_ip);

            $insert['login_num']    = 1;
            $insert['login_ip']     = $login_ip;
            $insert['login_time']   = $datetime;
            $insert['login_region'] = $ip_info['region'];
            $insert['create_time']  = $datetime;

            if ($member_wechat) {
                $member_id = $member_wechat['member_id'];
                $member_wechat_id = $member_wechat['member_wechat_id'];
                $username = 'wechat_mini_' . $member_wechat_id;

                if ($member_id) {
                    $member = Db::name('member')
                        ->field('member_id,login_num')
                        ->where(['member_id' => $member_id, 'is_delete' => 0])
                        ->find();
                    if ($member) {
                        $update['login_num']    = $member['login_num'] + 1;
                        $update['login_ip']     = $login_ip;
                        $update['login_time']   = $datetime;
                        $update['login_region'] = $ip_info['region'];
                        Db::name('member')
                            ->where('member_id', $member_id)
                            ->update($update);
                    } else {
                        $insert['username'] = $username;
                        $insert['password'] = '';
                        $member_id = Db::name('member')
                            ->insertGetId($insert);
                    }
                } else {
                    $insert['username'] = $username;
                    $insert['password'] = '';
                    $member_id = Db::name('member')
                        ->insertGetId($insert);
                }

                $update_wechat = $userinfo;
                $update_wechat['member_id'] = $member_id;
                $update_wechat['update_time'] = $datetime;
                Db::name('member_wechat')
                    ->where('member_wechat_id', $member_wechat_id)
                    ->update($update_wechat);
            } else {
                $insert_wechat = $userinfo;
                $insert_wechat['create_time'] = $datetime;
                $member_wechat_id = Db::name('member_wechat')
                    ->insertGetId($insert_wechat);

                $insert['reg_channel'] = 3;
                $insert['username']    = 'wechat_mini_' . $member_wechat_id;
                $insert['password']    = '';
                $member_id = Db::name('member')
                    ->insertGetId($insert);

                Db::name('member_wechat')
                    ->where('member_wechat_id', $member_wechat_id)
                    ->update(['member_id' => $member_id]);
            }

            $res = true;
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            // 回滚事务
            Db::rollback();
        }

        if (empty($res)) {
            exception($msg);
        }

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::loginField($member);
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
    public static function loginField($member)
    {
        $data = [];
        $field = ['member_id', 'username', 'nickname', 'phone', 'email', 'login_ip', 'login_time'];
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
