<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理
namespace app\common\service;

use think\facade\Db;
use app\common\cache\MemberCache;
use app\common\utils\IpInfoUtils;
use app\common\utils\DatetimeUtils;
use app\common\service\WechatService;
use app\common\service\file\FileService;
use app\common\model\MemberWechatModel;

class MemberService
{
    // 表名
    protected static $t_name = 'member';
    // 表主键
    protected static $t_pk = 'member_id';

    /**
     * 会员列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        if (empty($field)) {
            $field = self::$t_pk . ',username,nickname,phone,email,avatar_id,sort,remark,create_time,is_disable';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', self::$t_pk => 'desc'];
        }

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $pages = ceil($count / $limit);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $file_ids = array_column($list, 'avatar_id');
        $files = FileService::list([['file_id', 'in', $file_ids]], 1, $limit, [], 'file_id');

        $member_ids = array_column($list, self::$t_pk);
        $MemberWechat = new MemberWechatModel();
        $member_wechats = $MemberWechat
            ->field('member_id,nickname,headimgurl')
            ->where(self::$t_pk, 'in', $member_ids)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['avatar_url'] = '';
            if (isset($v['avatar_id'])) {
                foreach ($files['list'] as $kl => $vl) {
                    if ($v['avatar_id'] == $vl['file_id']) {
                        $list[$k]['avatar_url'] = $vl['file_url'];
                    }
                }
            }

            foreach ($member_wechats as $kmw => $vmw) {
                if ($v[self::$t_pk] == $vmw[self::$t_pk]) {
                    if (empty($list[$k]['avatar_url'])) {
                        $list[$k]['avatar_url'] = $vmw['headimgurl'];
                    }
                    if (empty($v['nickname'])) {
                        $list[$k]['nickname'] = $vmw['nickname'];
                    }
                }
            }
        }

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 会员信息
     *
     * @param integer $member_id 会员id
     * 
     * @return array
     */
    public static function info($member_id)
    {
        $member = MemberCache::get($member_id);
        if (empty($member)) {
            $member = Db::name(self::$t_name)
                ->where(self::$t_pk, $member_id)
                ->find();
            if (empty($member)) {
                exception('会员不存在：' . $member_id);
            }
            $member['avatar_url'] = FileService::fileUrl($member['avatar_id']);

            $MemberWechat = new MemberWechatModel();
            $member_wechat = $MemberWechat
                ->where(self::$t_pk, $member_id)
                ->find();
            if ($member_wechat) {
                if (empty($member['nickname'])) {
                    $member['nickname'] = $member_wechat['nickname'];
                }
                if (empty($member['avatar_url'])) {
                    $member['avatar_url'] = $member_wechat['headimgurl'];
                }
                $member_wechat['privilege'] = unserialize($member_wechat['privilege']);
                $member['wechat'] = $member_wechat;
            } else {
                $member['wechat'] = [];
            }

            // 0原密码修改密码，1直接设置新密码
            $member['pwd_edit_type'] = 0;
            if (empty($member['password'])) {
                $member['pwd_edit_type'] = 1;
            }

            MemberCache::set($member_id, $member);
        }

        return $member;
    }

    /**
     * 会员添加
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['password']    = md5($param['password']);
        $param['create_time'] = datetime();

        $member_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($member_id)) {
            exception();
        }

        $param[self::$t_pk] = $member_id;

        unset($param['password']);

        return $param;
    }

    /**
     * 会员修改
     *
     * @param array $param 会员信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $member_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $member_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $member_id;

        MemberCache::upd($member_id);

        return $param;
    }

    /**
     * 会员删除
     *
     * @param array $list 会员列表
     * 
     * @return array
     */
    public static function dele($list)
    {
        $pk_ids = array_column($list, self::$t_pk);

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $pk_ids)
            ->update($update);
        $MemberWechat = new MemberWechatModel();
        $MemberWechat->where(self::$t_pk, 'in', $pk_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $pk_ids;

        foreach ($pk_ids as $k => $v) {
            MemberCache::upd($v);
        }

        return $update;
    }

    /**
     * 会员设置地区
     *
     * @param array   $list      会员列表
     * @param integer $region_id 地区id
     * 
     * @return array
     */
    public static function region($list, $region_id = 0)
    {
        $pk_ids = array_column($list, self::$t_pk);

        $update['region_id']   = $region_id;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $pk_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $pk_ids;

        foreach ($pk_ids as $k => $v) {
            MemberCache::upd($v);
        }

        return $update;
    }

    /**
     * 会员是否禁用
     *
     * @param array   $list       会员列表
     * @param integer $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($list, $is_disable = 0)
    {
        $pk_ids = array_column($list, self::$t_pk);

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $pk_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $pk_ids;

        foreach ($pk_ids as $k => $v) {
            MemberCache::upd($v);
        }

        return $update;
    }

    /**
     * 会员修改密码
     *
     * @param array   $list     会员列表
     * @param integer $password 新密码
     * 
     * @return array
     */
    public static function repwd($list, $password)
    {
        $pk_ids = array_column($list, self::$t_pk);

        $update['password']    = md5($password);
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $pk_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update[self::$t_pk] = $pk_ids;
        $update['password']  = $password;

        foreach ($pk_ids as $k => $v) {
            MemberCache::upd($v);
        }

        return $update;
    }

    /**
     * 会员登录（账号）
     *
     * @param array $param 登录信息
     * 
     * @return array
     */
    public static function login($param)
    {
        // 通过 账号、手机、邮箱 登录
        $where[] = ['username|phone|email', '=', $param['username']];
        $where[] = ['password', '=', md5($param['password'])];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name(self::$t_name)
            ->field('member_id,username,nickname,phone,email,login_num,is_disable')
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
        Db::name(self::$t_name)
            ->where('member_id', '=', $member_id)
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
     * 会员微信登录
     *
     * @param array $userinfo 会员微信信息
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
        $MemberWechat = new MemberWechatModel();
        $member_wechat = $MemberWechat
            ->field('member_wechat_id,member_id')
            ->where($wechat_where)
            ->find();

        // 启动事务
        $errmsg = '';
        Db::startTrans();
        try {
            if ($member_wechat) {
                $member_wechat_id = $member_wechat['member_wechat_id'];
                $MemberWechat = new MemberWechatModel();
                $MemberWechat->where('member_wechat_id', '=', $member_wechat_id)
                    ->update($userinfo);
                $member_id = $member_wechat['member_id'];
            } else {
                $insert_wechat = $userinfo;
                $insert_wechat['create_time'] = $datetime;
                $MemberWechat = new MemberWechatModel();
                $member_wechat_id = $MemberWechat
                    ->insertGetId($insert_wechat);
                $member_id = 0;
            }

            $member = Db::name(self::$t_name)
                ->field('member_id,nickname,login_num')
                ->where('member_id', '=', $member_id)
                ->where('is_delete', '=', 0)
                ->find();
            if ($member) {
                if (empty($member['nickname'])) {
                    $member_update['nickname'] = $userinfo['nickname'];
                }
                $member_update['login_num']    = $member['login_num'] + 1;
                $member_update['login_ip']     = $login_ip;
                $member_update['login_time']   = $datetime;
                $member_update['login_region'] = $ip_info['region'];
                Db::name(self::$t_name)
                    ->where('member_id', '=', $member_id)
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
                $member_id = Db::name(self::$t_name)
                    ->insertGetId($member_insert);
                // 注册日志
                $member_log['member_id'] = $member_id;
                MemberLogService::add($member_log, 1);
            }

            $wechat_update = $userinfo;
            $wechat_update['member_id']   = $member_id;
            $wechat_update['update_time'] = $datetime;
            $MemberWechat = new MemberWechatModel();
            $MemberWechat->where('member_wechat_id', $member_wechat_id)
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
     * 会员退出
     *
     * @param integer $member_id 会员id
     * 
     * @return array
     */
    public static function logout($member_id)
    {
        $update['logout_time'] = datetime();

        Db::name(self::$t_name)
            ->where('member_id', $member_id)
            ->update($update);

        MemberCache::del($member_id);

        $update['member_id'] = $member_id;

        return $update;
    }

    /**
     * 绑定手机（小程序）
     *
     * @param string  $code
     * @param string  $iv
     * @param string  $encrypted_data
     * @param integer $member_id
     *
     * @return array
     */
    public static function bindPhoneMini($code, $iv, $encrypted_data, $member_id = 0)
    {
        if (empty($member_id)) {
            $member_id = member_id();
        }

        $app = WechatService::mini();
        $session = $app->auth->session($code);
        $decrypted_data = $app->encryptor->decryptData($session['session_key'], $iv, $encrypted_data);
        if (isset($decrypted_data['phoneNumber'])) {
            $phone = $decrypted_data['phoneNumber'];
            $phone_exist = Db::name(self::$t_name)
                ->field('phone')
                ->where(self::$t_pk, '<>', $member_id)
                ->where('phone', '=', $phone)
                ->find();
            if ($phone_exist) {
                exception('手机号已存在');
            }
            Db::name(self::$t_name)
                ->where(self::$t_pk, $member_id)
                ->update(['phone' => $phone, 'update_time' => datetime()]);
            return $decrypted_data;
        } else {
            exception('绑定失败');
        }
    }

    /**
     * 会员统计（数量）
     *
     * @param string $date 日期
     * @param string $type 类型：new新增，act活跃
     *
     * @return integer
     */
    public static function statNum($date = 'total', $type = 'new')
    {
        $key  = $date . ':' . $type;
        $data = MemberCache::get($key);
        if (empty($data)) {
            $where[] = ['is_delete', '=', 0];
            if ($date == 'total') {
                $where[] = [self::$t_pk, '>', 0];
            } else {
                if ($date == 'yesterday') {
                    $yesterday = DatetimeUtils::yesterday();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($yesterday);
                } elseif ($date == 'thisweek') {
                    list($start, $end) = DatetimeUtils::thisWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastweek') {
                    list($start, $end) = DatetimeUtils::lastWeek();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'thismonth') {
                    list($start, $end) = DatetimeUtils::thisMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } elseif ($date == 'lastmonth') {
                    list($start, $end) = DatetimeUtils::lastMonth();
                    $sta_time = DatetimeUtils::datetime($start);
                    $sta_time = $sta_time[0];
                    $end_time = DatetimeUtils::datetime($end);
                    $end_time = $end_time[1];
                } else {
                    $today = DatetimeUtils::today();
                    list($sta_time, $end_time) = DatetimeUtils::datetime($today);
                }

                if ($type == 'act') {
                    $where[] = ['login_time', '>=', $sta_time];
                    $where[] = ['login_time', '<=', $end_time];
                } else {
                    $where[] = ['create_time', '>=', $sta_time];
                    $where[] = ['create_time', '<=', $end_time];
                }
            }

            $data = Db::name(self::$t_name)
                ->field(self::$t_pk)
                ->where($where)
                ->count(self::$t_pk);

            MemberCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员统计（日期）
     *
     * @param array $date 日期范围
     * 
     * @return array
     */
    public static function statDate($date = [])
    {
        if (empty($date)) {
            $date[0] = DatetimeUtils::daysAgo(29);
            $date[1] = DatetimeUtils::today();
        }
        $sta_date = $date[0];
        $end_date = $date[1];

        $key  = 'date:' . $sta_date . '-' . $end_date;
        $data = MemberCache::get($key);
        if (empty($data)) {
            $data['date'] = $date;
            $dates = DatetimeUtils::betweenDates($sta_date, $end_date);
            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            // 新增会员
            $new = Db::name(self::$t_name)
                ->field("count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date")
                ->where('create_time', '>=', $sta_time)
                ->where('create_time', '<=', $end_time)
                ->group("date_format(create_time,'%Y-%m-%d')")
                ->select()
                ->toArray();
            $new_x = $new_s = [];
            foreach ($dates as $k => $v) {
                $new_x[$k] = $v;
                $new_s[$k] = 0;
                foreach ($new as $kn => $vn) {
                    if ($v == $vn['date']) {
                        $new_s[$k] = $vn['num'];
                    }
                }
            }
            $data['new'] = ['x' => $new_x, 's' => $new_s];

            // 活跃会员
            $act = Db::name(self::$t_name)
                ->field("count(login_time) as num, date_format(login_time,'%Y-%m-%d') as date")
                ->where('login_time', '>=', $sta_time)
                ->where('login_time', '<=', $end_time)
                ->group("date_format(login_time,'%Y-%m-%d')")
                ->select()
                ->toArray();
            $act_x = $act_s = [];
            foreach ($dates as $k => $v) {
                $act_x[$k] = $v;
                $act_s[$k] = 0;
                foreach ($act as $ka => $va) {
                    if ($v == $va['date']) {
                        $act_s[$k] = $va['num'];
                    }
                }
            }
            $data['act'] = ['x' => $act_x, 's' => $act_s];

            // 会员总数
            $count_x = $count_s = [];
            foreach ($dates as $k => $v) {
                $count_t = DatetimeUtils::dateEndTime($v);
                $count_x[] = $v;
                $count_s[] = Db::name(self::$t_name)
                    ->where('is_delete', '=', 0)
                    ->where('create_time', '<=', $count_t)
                    ->count(self::$t_pk);
            }
            $data['count'] = ['x' => $count_x, 's' => $count_s];

            MemberCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 会员统计（总数）
     *
     * @return array
     */
    public static function statCount()
    {
        $month = DatetimeUtils::months();
        $key   = 'count:' . reset($month) . '-' . end($month);
        $data  = MemberCache::get($key);
        if (empty($data)) {
            $x = $s = [];
            foreach ($month as $k => $v) {
                $time = DatetimeUtils::monthStartEnd($v);
                $time = DatetimeUtils::dateEndTime($time[1]);
                $x[] = $v;
                $s[] = Db::name(self::$t_name)
                    ->where('is_delete', '=', 0)
                    ->where('create_time', '<=', $time)
                    ->count(self::$t_pk);
            }
            $data['x'] = $x;
            $data['s'] = $s;

            MemberCache::set($key, $data);
        }

        return $data;
    }
}
