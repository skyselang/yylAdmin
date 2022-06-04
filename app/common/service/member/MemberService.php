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
            $field = $pk . ',username,nickname,phone,email,avatar_id,sort,remark,is_disable,create_time,delete_time';
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        $avatar_ids = array_column($list, 'avatar_id');
        $file = FileService::fileArray($avatar_ids);
        $file = array_column($file, 'file_url', 'file_id');

        $member_ids = array_column($list, $pk);
        $WechatModel = new WechatModel();
        $member_wechat = $WechatModel->field($pk . ',nickname,headimgurl')->where($pk, 'in', $member_ids)->select()->toArray();
        $headimgurl = array_column($member_wechat, 'headimgurl', $pk);
        $nickname = array_column($member_wechat, 'nickname', $pk);

        foreach ($list as $k => $v) {
            $list[$k]['avatar_url'] = $file[$v['avatar_id']] ?? '';
            if (empty($list[$k]['avatar_url'])) {
                $list[$k]['avatar_url'] = $headimgurl[$v[$pk]] ?? '';
            }
            if (empty($v['nickname'])) {
                $list[$k]['nickname'] = $nickname[$v[$pk]] ?? '';
            }
        }

        $reg_channels = $model->reg_channel_arr;
        $reg_types = $model->reg_type_arr;

        return compact('count', 'pages', 'page', 'limit', 'list', 'reg_channels', 'reg_types');
    }

    /**
     * 会员信息
     *
     * @param int  $id   会员id
     * @param bool $exce 不存在是否抛出异常
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
            $info = $info->append(['gender_name', 'reg_channel_name', 'reg_type_name'])->toArray();

            $info['avatar_url'] = FileService::fileUrl($info['avatar_id']);

            $info['wechat'] = [];
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
     * @param mixed $ids    会员id
     * @param array $update 会员信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        MemberCache::del($ids);

        return $update;
    }

    /**
     * 会员删除
     *
     * @param mixed $ids  会员id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        $errmsg = '';
        // 启动事务
        $model::startTrans();
        try {
            $WechatModel = new WechatModel();
            if ($real) {
                $model->where($pk, 'in', $ids)->delete();
                $WechatModel->where($pk, 'in', $ids)->delete();
            } else {
                $update['is_delete']   = 1;
                $update['delete_time'] = datetime();
                $model->where($pk, 'in', $ids)->update($update);
                $WechatModel->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model::commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model::rollback();
        }

        if ($errmsg) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        MemberCache::del($ids);

        return $update;
    }

    /**
     * 会员登录
     *
     * @param array  $param 登录信息
     * @param string $type  登录方式
     * 
     * @return array
     */
    public static function login($param, $type = '')
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        if ($type == 'username') {
            // 通过用户名登录
            $where[] = ['username', '=', $param['username']];
            $where[] = ['password', '=', md5($param['password'])];
        } else if ($type == 'phone') {
            // 通过手机登录
            $where[] = ['phone', '=', $param['phone']];
            if (isset($param['password'])) {
                $where[] = ['password', '=', md5($param['password'])];
            }
        } else if ($type == 'email') {
            // 通过邮箱登录
            $where[] = ['email', '=', $param['email']];
            if (isset($param['password'])) {
                $where[] = ['password', '=', md5($param['password'])];
            }
        } else {
            // 通过用户名、手机、邮箱登录
            $where[] = ['username|phone|email', '=', $param['account']];
            $where[] = ['password', '=', md5($param['password'])];
        }
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
        $reg_type    = $userinfo['reg_type'];
        $ip_info     = IpInfoUtils::info($login_ip);

        foreach ($userinfo as $k => $v) {
            if ($k == 'privilege') {
                $userinfo[$k] = serialize($v);
            }
        }
        unset($userinfo['login_ip'], $userinfo['reg_channel'], $userinfo['reg_type']);

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

            $member_field = $MemberPk . ',nickname,login_num,is_disable,is_delete';
            $member_where[] = [$MemberPk, '=', $member_id];
            $member_where[] = ['is_delete', '=', 0];
            $member = $MemberModel->field($member_field)->where($member_where)->find();
            if ($member) {
                $member = $member->toArray();
                if ($member['is_disable']) {
                    exception('账号已被禁用');
                }
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
                $wx_username = $member_wechat_id . '-' . uniqid();
                if ($reg_channel == 2) {
                    $member_insert['username'] = 'wxoffi' . $wx_username;
                } elseif ($reg_channel == 3) {
                    $member_insert['username'] = 'wxmini' . $wx_username;
                } else {
                    $member_insert['username'] = 'wx' . $wx_username;
                }
                $member_insert['login_num']    = 1;
                $member_insert['login_ip']     = $login_ip;
                $member_insert['login_time']   = $datetime;
                $member_insert['login_region'] = $ip_info['region'];
                $member_insert['create_time']  = $datetime;
                $member_insert['reg_channel']  = $reg_channel;
                $member_insert['reg_type']     = $reg_type;
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
            $errmsg = '微信登录失败：' . $e->getMessage();
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
        $data = [];
        $field = ['member_id', 'username', 'nickname', 'phone', 'email', 'login_ip', 'login_time', 'login_num', 'avatar_url'];
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

        $update[$pk] = $id;

        MemberCache::del($id);

        return $update;
    }

    /**
     * 绑定手机（小程序）
     *
     * @param string $code
     * @param int    $member_id
     *
     * @return array
     */
    public static function bindPhoneMini($code, $member_id = 0)
    {
        if (empty($member_id)) {
            $member_id = member_id();
        }

        $app = WechatService::mini();

        // 获取 access token 实例
        $accessToken = $app->access_token;
        $token = $accessToken->getToken(); // token 数组 token['access_token'] 字符串
        // 获取手机号（新版本接口）
        $res = http_post('https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $token['access_token'], ['code' => $code]);
        $errcode = $res['errcode'] ?? 0;
        if ($errcode) {
            exception('绑定失败:' . $errcode . ',' . $res['errmsg'] ?? '');
        }

        $phone = $res['phone_info']['phoneNumber'] ?? 0;
        if ($phone) {
            $model = new MemberModel();
            $pk = $model->getPk();

            $phone_where[] = [$pk, '<>', $member_id];
            $phone_where[] = ['phone', '=', $phone];
            $phone_where[] = ['is_delete', '=', 0];
            $phone_exist = $model->field('phone')->where($phone_where)->find();
            if ($phone_exist) {
                exception('手机号已存在：' . $phone);
            }

            $model->where($pk, $member_id)->update(['phone' => $phone, 'update_time' => datetime()]);

            return $res;
        } else {
            exception('绑定失败');
        }
    }

    /**
     * 会员统计
     *
     * @param string $type 日期类型：day，month
     * @param array  $date 日期范围：[开始日期，结束日期]
     * @param string $stat 统计类型：count总计，number数量，reg_channel注册渠道，reg_type注册方式
     * 
     * @return array
     */
    public static function stat($type = 'month', $date = [], $stat = 'count')
    {
        if (empty($date)) {
            if ($type == 'day') {
                $date[0] = date('Y-m-d', strtotime('-29 days'));
                $date[1] = date('Y-m-d');
            } else {
                $date[0] = date('Y-m', strtotime('-11 months'));
                $date[1] = date('Y-m');
            }
        }
        $sta_date = $date[0];
        $end_date = $date[1];

        $key = $type . ':' . $stat . $sta_date . '-' . $end_date;
        $data = MemberCache::get($key);
        if (empty($data)) {
            $dates = [];

            if ($type == 'day') {
                $s_time = strtotime(date('Y-m-d', strtotime($sta_date)));
                $e_time = strtotime(date('Y-m-d', strtotime($end_date)));
                while ($s_time <= $e_time) {
                    $dates[] = date('Y-m-d', $s_time);
                    $s_time = strtotime('next day', $s_time);
                }

                $field = "count(create_time) as num, date_format(create_time,'%Y-%m-%d') as date";
                $group = "date_format(create_time,'%Y-%m-%d')";
            } else {
                $s_time = strtotime(date('Y-m-01', strtotime($sta_date)));
                $e_time = strtotime(date('Y-m-01', strtotime($end_date)));
                while ($s_time <= $e_time) {
                    $dates[] = date('Y-m', $s_time);
                    $s_time = strtotime('next month', $s_time);
                }

                $sta_date = date('Y-m-01', strtotime($sta_date));
                $end_date = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($end_date)))));

                $field = "count(create_time) as num, date_format(create_time,'%Y-%m') as date";
                $group = "date_format(create_time,'%Y-%m')";
            }

            $where[] = ['is_delete', '=', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];

            $model = new MemberModel();
            $pk = $model->getPk();

            if ($stat == 'count') {
                $data = [
                    ['date' => 'total', 'name' => '会员', 'title' => '总数', 'count' => 0],
                    ['date' => 'online', 'name' => '在线', 'title' => '数量', 'count' => 0],
                    ['date' => 'today', 'name' => '今天', 'title' => '新增', 'count' => 0],
                    ['date' => 'yesterday', 'name' => '昨天', 'title' => '新增', 'count' => 0],
                    ['date' => 'thisweek', 'name' => '本周', 'title' => '新增', 'count' => 0],
                    ['date' => 'lastweek', 'name' => '上周', 'title' => '新增', 'count' => 0],
                    ['date' => 'thismonth', 'name' => '本月', 'title' => '新增', 'count' => 0],
                    ['date' => 'lastmonth', 'name' => '上月', 'title' => '新增', 'count' => 0],
                ];

                foreach ($data as $k => $v) {
                    $where = [];
                    $where = [['is_delete', '=', 0]];

                    if ($v['date'] == 'total') {
                        $where[] = [$pk, '>', 0];
                    } elseif ($v['date'] == 'online') {
                        $where[] = ['login_time', '>=', date('Y-m-d H:i:s', time() - 3600)];
                        $where[] = ['login_time', '<=', date('Y-m-d H:i:s')];
                    } else {
                        if ($v['date'] == 'yesterday') {
                            $sta_date = $end_date = date('Y-m-d', strtotime('-1 day'));
                        } elseif ($v['date'] == 'thisweek') {
                            $sta_date = date('Y-m-d', strtotime('this week'));
                            $end_date = date('Y-m-d', strtotime('last day next week +0 day'));
                        } elseif ($v['date'] == 'lastweek') {
                            $sta_date = date('Y-m-d', strtotime('last week'));
                            $end_date = date('Y-m-d', strtotime('last day last week +7 day'));
                        } elseif ($v['date'] == 'thismonth') {
                            $sta_date = date('Y-m-01');
                            $end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', strtotime('next month')))));
                        } elseif ($v['date'] == 'lastmonth') {
                            $sta_date = date('Y-m-01', strtotime('last month'));
                            $end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01', time()))));
                        } else {
                            $sta_date = $end_date = date('Y-m-d');
                        }

                        $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
                        $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
                    }

                    $data[$k]['count'] = $model->where($where)->count();
                }

                MemberCache::set($key, $data, 120);

                return $data;
            } elseif ($stat == 'number') {
                $data['title'] = '数量';
                $add = $total = [];
                // 新增会员
                $adds = $model
                    ->field($field)
                    ->where($where)
                    ->group($group)
                    ->select()
                    ->column('num', 'date');
                // 会员总数
                foreach ($dates as $k => $v) {
                    $add[$k] = $adds[$v] ?? 0;

                    if ($type == 'month') {
                        $e_t = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($v)))));
                    } else {
                        $e_t = $v;
                    }
                    $total[$k] = $model->where('is_delete', 0)->where('create_time', '<=', $e_t . ' 23:59:59')->count();
                }

                $series = [
                    ['name' => '会员总数', 'type' => 'line', 'data' => $total, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '新增会员', 'type' => 'line', 'data' => $add, 'label' => ['show' => true, 'position' => 'top']],
                ];
            } elseif ($stat == 'reg_channel') {
                $data['title'] = '注册渠道';

                $series = [];
                $reg_channel_arr = $model->reg_channel_arr;
                foreach ($reg_channel_arr as $k => $v) {
                    $series[] = ['name' => $v, 'reg_channel' => $k, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']];
                }

                foreach ($series as $k => $v) {
                    $series_data = $model
                        ->field($field)
                        ->where($where)
                        ->where('reg_channel', $v['reg_channel'])
                        ->group($group)
                        ->select()
                        ->column('num', 'date');
                    foreach ($dates as $kx => $vx) {
                        $series[$k]['data'][$kx] = $series_data[$vx] ?? 0;
                    }
                }
            } elseif ($stat == 'reg_type') {
                $data['title'] = '注册方式';

                $series = [];
                $reg_type_arr = $model->reg_type_arr;
                foreach ($reg_type_arr as $k => $v) {
                    $series[] = ['name' => $v, 'reg_type' => $k, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']];
                }

                foreach ($series as $k => $v) {
                    $series_data = $model
                        ->field($field)
                        ->where($where)
                        ->where('reg_type', $v['reg_type'])
                        ->group($group)
                        ->select()
                        ->column('num', 'date');
                    foreach ($dates as $kx => $vx) {
                        $series[$k]['data'][$kx] = $series_data[$vx] ?? 0;
                    }
                }
            }

            $legend = array_column($series, 'name');

            $data['type']   = $type;
            $data['date']   = $date;
            $data['legend'] = $legend;
            $data['xAxis']  = $dates;
            $data['series'] = $series;

            MemberCache::set($key, $data);
        }

        return $data;
    }
}
