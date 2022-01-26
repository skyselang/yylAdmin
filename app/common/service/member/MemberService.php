<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理
namespace app\common\service\member;

use app\common\utils\IpInfoUtils;
use app\common\utils\DatetimeUtils;
use app\common\cache\member\MemberCache;
use app\common\service\setting\WechatService;
use app\common\service\setting\TokenService;
use app\common\service\file\FileService;
use app\common\model\member\MemberModel;
use app\common\model\member\WechatModel;

class MemberService
{
    /**
     * 会员列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '')
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',username,nickname,phone,email,avatar_id,sort,remark,is_disable,create_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);

        $pages = ceil($count / $limit);

        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        $member_ids = array_column($list, $pk);
        $WechatModel = new WechatModel();
        $member_wechat = $WechatModel->field($pk . ',nickname,headimgurl')->where($pk, 'in', $member_ids)->select()->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['avatar_url'] = '';
            if (isset($v['avatar_id'])) {
                $list[$k]['avatar_url'] = FileService::fileUrl($v['avatar_id']);
            }

            foreach ($member_wechat as $kmw => $vmw) {
                if ($v[$pk] == $vmw[$pk]) {
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
     * @param int $id 会员id
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = MemberCache::get($id);
        if (empty($info)) {
            $model = new MemberModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('会员不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();
            $info['avatar_url'] = FileService::fileUrl($info['avatar_id']);

            $WechatModel = new WechatModel();
            $member_wechat = $WechatModel->where($pk, $id)->find();
            if ($member_wechat) {
                if (empty($info['nickname'])) {
                    $info['nickname'] = $member_wechat['nickname'];
                }
                if (empty($info['avatar_url'])) {
                    $info['avatar_url'] = $member_wechat['headimgurl'];
                }
                $member_wechat['privilege'] = unserialize($member_wechat['privilege']);
                $info['wechat'] = $member_wechat;
            } else {
                $info['wechat'] = [];
            }

            // 0原密码修改密码，1直接设置新密码
            $info['pwd_edit_type'] = 0;
            if (empty($info['password'])) {
                $info['pwd_edit_type'] = 1;
            }

            MemberCache::set($id, $info);
        }

        return $info;
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
        $model = new MemberModel();
        $pk = $model->getPk();

        $param['password']    = md5($param['password']);
        $param['create_time'] = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

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
        $model = new MemberModel();
        $pk = $model->getPk();

        $id = $param[$pk];
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        MemberCache::upd($id);

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 会员删除
     *
     * @param array $ids 会员id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        $WechatModel = new WechatModel();
        $WechatModel->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            MemberCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 会员修改地区
     *
     * @param array $ids       会员id
     * @param int   $region_id 地区id
     * 
     * @return array
     */
    public static function region($ids, $region_id = 0)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        $update['region_id']   = $region_id;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            MemberCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 会员修改密码
     *
     * @param array $ids      会员id
     * @param int   $password 新密码
     * 
     * @return array
     */
    public static function repwd($ids, $password)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        $update['password']    = md5($password);
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            MemberCache::upd($v);
        }

        $update['ids']      = $ids;
        $update['password'] = $password;

        return $update;
    }

    /**
     * 会员是否禁用
     *
     * @param array $ids        会员id
     * @param int   $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable = 0)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            MemberCache::upd($v);
        }

        $update['ids'] = $ids;

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
        $model = new MemberModel();
        $pk = $model->getPk();

        // 通过 账号、手机、邮箱 登录
        $where[] = ['username|phone|email', '=', $param['username']];
        $where[] = ['password', '=', md5($param['password'])];
        $where[] = ['is_delete', '=', 0];

        $field = $pk . ',username,nickname,phone,email,login_num,is_disable';
        $member = $model->field($field)->where($where)->find();
        if (empty($member)) {
            exception('账号或密码错误');
        }
        $member = $member->toArray();
        if ($member['is_disable'] == 1) {
            exception('账号已被禁用');
        }

        $ip_info   = IpInfoUtils::info();
        $member_id = $member[$pk];

        // 登录信息
        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_num']    = $member['login_num'] + 1;
        $update['login_time']   = datetime();
        $model->where($pk, $member_id)->update($update);

        // 登录日志
        $member_log[$pk] = $member_id;
        LogService::add($member_log, 2);

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::field($member);
        $data['api_token'] = TokenService::create($member);

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
        }
        unset($userinfo['login_ip'], $userinfo['reg_channel']);

        $MemberModel = new MemberModel();
        $MemberPk = $MemberModel->getPk();

        // 会员微信
        if ($unionid) {
            $wechat_where[] = ['unionid', '=', $unionid];
        } else {
            $wechat_where[] = ['openid', '=', $openid];
        }
        $wechat_where[] = ['is_delete', '=', 0];
        $WechatModel = new WechatModel();
        $MemberWechatPk = $WechatModel->getPk();
        $member_wechat = $WechatModel->field($MemberWechatPk . ',' . $MemberPk)->where($wechat_where)->find();

        $errmsg = '';
        // 启动事务
        $MemberModel->startTrans();
        try {
            if ($member_wechat) {
                $member_wechat_id = $member_wechat[$MemberWechatPk];
                $WechatModel->where($MemberWechatPk, $member_wechat_id)->update($userinfo);
                $member_id = $member_wechat[$MemberPk];
            } else {
                $wechat_insert = $userinfo;
                $wechat_insert['create_time'] = $datetime;
                $member_wechat_id = $WechatModel->insertGetId($wechat_insert);
                $member_id = 0;
            }

            $member_field = $MemberPk . ',nickname,login_num';
            $member_where[] = [$MemberPk, '=', $member_id];
            $member_where[] = ['is_delete', '=', 0];
            $member = $MemberModel->field($member_field)->where($member_where)->find();
            if ($member) {
                $member = $member->toArray();
                if (empty($member['nickname'])) {
                    $member_update['nickname'] = $userinfo['nickname'];
                }
                $member_update['login_num']    = $member['login_num'] + 1;
                $member_update['login_ip']     = $login_ip;
                $member_update['login_time']   = $datetime;
                $member_update['login_region'] = $ip_info['region'];
                $MemberModel->where($MemberPk, $member_id)->update($member_update);
                // 登录日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, 2);
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
                $member_insert['nickname']     = $userinfo['nickname'] ?: $member_insert['username'];
                $member_insert['password']     = '';
                $member_id = $MemberModel->insertGetId($member_insert);
                // 注册日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, 1);
            }

            $wechat_update = $userinfo;
            $wechat_update[$MemberPk]     = $member_id;
            $wechat_update['update_time'] = $datetime;
            $WechatModel->where($MemberWechatPk, $member_wechat_id)->update($wechat_update);

            // 提交事务
            $MemberModel->commit();
        } catch (\Exception $e) {
            $errmsg = '微信登录失败：' . $e->getMessage() . '：' . $e->getLine();
            // 回滚事务
            $MemberModel->rollback();
        }

        if ($errmsg) {
            exception($errmsg);
        }

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::field($member);
        $data['api_token'] = TokenService::create($member);

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
        $model = new MemberModel();
        $pk = $model->getPk();

        $data = [];
        $field = [$pk, 'username', 'nickname', 'phone', 'email', 'login_ip', 'login_time', 'login_num', 'avatar_url'];
        foreach ($field as $v) {
            $data[$v] = $member[$v];
        }

        return $data;
    }

    /**
     * 会员退出
     *
     * @param int $id 会员id
     * 
     * @return array
     */
    public static function logout($id)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        $update['logout_time'] = datetime();

        $model->where($pk, $id)->update($update);

        MemberCache::del($id);

        $update[$pk] = $id;

        return $update;
    }

    /**
     * 绑定手机（小程序）
     *
     * @param string $code
     * @param string $iv
     * @param string $encrypted_data
     * @param int    $member_id
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
            $model = new MemberModel();
            $pk = $model->getPk();

            $phone = $decrypted_data['phoneNumber'];
            $phone_where[] = [$pk, '<>', $member_id];
            $phone_where[] = ['phone', '=', $phone];
            $phone_where[] = ['is_delete', '=', 0];
            $phone_exist = $model->field('phone')->where($phone_where)->find();
            if ($phone_exist) {
                exception('手机号已存在：' . $phone);
            }

            $model->where($pk, $member_id)->update(['phone' => $phone, 'update_time' => datetime()]);

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
     * @return int
     */
    public static function statNum($date = 'total', $type = 'new')
    {
        $key = $date . ':' . $type;
        $data = MemberCache::get($key);
        if (empty($data)) {
            $model = new MemberModel();
            $pk = $model->getPk();

            $where[] = ['is_delete', '=', 0];
            if ($date == 'total') {
                $where[] = [$pk, '>', 0];
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

            $data = $model->where($where)->count($pk);

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

        $key = 'date:' . $sta_date . '-' . $end_date;
        $data = MemberCache::get($key);
        if (empty($data)) {
            $data['date'] = $date;
            $dates = DatetimeUtils::betweenDates($sta_date, $end_date);
            $sta_time = DatetimeUtils::dateStartTime($sta_date);
            $end_time = DatetimeUtils::dateEndTime($end_date);

            $model = new MemberModel();
            $pk = $model->getPk();

            // 新增会员
            $new = $model
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
                foreach ($new as $vn) {
                    if ($v == $vn['date']) {
                        $new_s[$k] = $vn['num'];
                    }
                }
            }
            $data['new'] = ['x' => $new_x, 's' => $new_s];

            // 活跃会员
            $act = $model
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
                foreach ($act as $va) {
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
                $count_s[] = $model->where('is_delete', 0)->where('create_time', '<=', $count_t)->count($pk);
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
            $model = new MemberModel();
            $pk = $model->getPk();

            $x = $s = [];
            foreach ($month as $v) {
                $time = DatetimeUtils::monthStartEnd($v);
                $time = DatetimeUtils::dateEndTime($time[1]);
                $x[] = $v;
                $s[] = $model->where('is_delete', 0)->where('create_time', '<=', $time)->count($pk);
            }
            $data['x'] = $x;
            $data['s'] = $s;

            MemberCache::set($key, $data);
        }

        return $data;
    }
}
