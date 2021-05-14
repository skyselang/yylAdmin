<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-14
 */

namespace app\index\service;

use think\facade\Db;
use app\common\cache\MemberCache;
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
            exception('会员已被禁用');
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

        MemberCache::del($member_id);
        $member = MemberService::info($member_id);

        return $member;
    }

    /**
     * 公众号登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return void
     */
    public static function offiLogin($userinfo)
    {
        $openid   = $userinfo['openid'];
        $login_ip = $userinfo['login_ip'];

        unset($userinfo['login_ip']);

        // 会员微信信息
        $member_wechat = Db::name('member_wechat')
            ->field('member_wechat_id,member_id')
            ->where(['openid' => $openid, 'is_delete' => 0])
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

            MemberCache::del($member_id);

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

        $member = MemberService::info($member_id);

        $data['member_id']    = $member['member_id'];
        $data['username']     = $member['username'];
        $data['nickname']     = $member['nickname'];
        $data['avatar']       = $member['avatar'];
        $data['member_token'] = $member['member_token'];

        return $data;
    }

    /**
     * 小程序登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return void
     */
    public static function miniLogin($userinfo)
    {
        $openid   = $userinfo['openid'];
        $login_ip = $userinfo['login_ip'];

        unset($userinfo['login_ip']);

        $member_wechat = Db::name('member_wechat')
            ->field('member_wechat_id,member_id')
            ->where(['openid' => $openid, 'is_delete' => 0])
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

            MemberCache::del($member_id);

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

        $member = MemberService::info($member_id);

        $data['member_id']    = $member['member_id'];
        $data['username']     = $member['username'];
        $data['nickname']     = $member['nickname'];
        $data['avatar']       = $member['avatar'];
        $data['member_token'] = $member['member_token'];

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

        $update['member_id'] = $member_id;

        MemberCache::del($member_id);

        return $update;
    }
}
