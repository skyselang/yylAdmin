<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Validate;
use app\common\cache\member\MemberCache;
use app\common\service\utils\IpInfoUtils;
use app\common\service\setting\WechatService;
use app\common\service\member\SettingService;
use app\common\model\member\MemberModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员管理
 */
class MemberService
{
    /**
     * 添加、修改字段
     * @var array
     */
    public static $edit_field = [
        'member_id/d' => 0,
        'avatar_id/d' => 0,
        'nickname/s'  => '',
        'username/s'  => '',
        'phone/s'     => '',
        'email/s'     => '',
        'name/s'      => '',
        'gender/d'    => 0,
        'region_id/d' => 0,
        'remark/s'    => '',
        'sort/d'      => 250,
        'tag_ids/a'   => [],
        'group_ids/a' => []
    ];

    /**
     * 会员列表
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
        $model = new MemberModel();
        $pk = $model->getPk();
        $group = 'm.' . $pk;

        if (empty($field)) {
            $field = $group . ',headimgurl,avatar_id,nickname,username,phone,email,sort,is_super,is_disable,create_time';
        }
        if (empty($order)) {
            $order = [$group => 'desc'];
        }

        $model = $model->alias('m');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'tag_ids' && is_array($wv[2])) {
                $model = $model->join('member_attributes t', 'm.member_id=t.member_id')->where('t.tag_id', 'in', $wv[2]);
                unset($where[$wk]);
            }
            if ($wv[0] == 'group_ids' && is_array($wv[2])) {
                $model = $model->join('member_attributes g', 'm.member_id=g.member_id')->where('g.group_id', 'in', $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $count = $model->where($where)->group($group)->count();
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)
            ->with(['avatar', 'tags', 'groups'])
            ->append(['avatar_url', 'tag_names', 'group_names'])
            ->hidden(['avatar', 'tags', 'groups'])
            ->page($page)->limit($limit)->order($order)->group($group)->select()->toArray();

        $genders = SettingService::genders();
        $reg_types = SettingService::reg_types();
        $reg_channels = SettingService::reg_channels();

        return compact('count', 'pages', 'page', 'limit', 'list', 'genders', 'reg_types', 'reg_channels');
    }

    /**
     * 会员信息
     *
     * @param int  $id    会员id
     * @param bool $exce  不存在是否抛出异常
     * @param bool $group 是否返回分组信息
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true, $group = false)
    {
        $info = MemberCache::get($id);
        if (empty($info)) {
            $model = new MemberModel();

            $info = $model->with(['avatar', 'tags', 'groups'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('会员不存在：' . $id);
                }
                return [];
            }
            $info = $info
                ->append(['avatar_url', 'tag_ids', 'tag_names', 'group_ids', 'group_names', 'gender_name', 'reg_type_name', 'reg_channel_name'])
                ->hidden(['avatar', 'tags', 'groups'])
                ->toArray();

            // 0原密码修改密码，1直接设置新密码
            $info['pwd_edit_type'] = 0;
            if (empty($info['password'])) {
                $info['pwd_edit_type'] = 1;
            }

            if (member_is_super($id)) {
                $api_list = ApiService::list('list', [where_delete()], [], 'api_url');
                $api_ids  = array_column($api_list, 'api_id');
                $api_urls = array_column($api_list, 'api_url');
            } elseif ($info['is_super'] == 1) {
                $api_list = ApiService::list('list', where_disdel(), [], 'api_url');
                $api_ids  = array_column($api_list, 'api_id');
                $api_urls = array_column($api_list, 'api_url');
            } else {
                $api_ids  = GroupService::api_ids($info['group_ids'], where_disdel());
                $api_list = ApiService::list('list', where_disdel(['api_id', 'in', $api_ids]), [], 'api_url');
                $api_urls = array_column($api_list, 'api_url');
            }

            $api_ids  = array_values(array_filter($api_ids));
            $api_urls = array_values(array_filter($api_urls));

            $setting = SettingService::info();
            $token_name = $setting['token_name'];

            $info['api_ids']   = $api_ids;
            $info['api_urls']  = $api_urls;
            $info[$token_name] = TokenService::create($info);

            MemberCache::set($id, $info);
        }

        if ($group) {
            $member_api_ids = $info['api_ids'] ?? [];
            $group_api_ids  = GroupService::api_ids($info['group_ids'], where_disdel());

            $api_list = ApiService::list('list', [where_delete()], [], 'api_id,api_pid,api_name,api_url,is_unlogin,is_unauth');
            foreach ($api_list as &$val) {
                $val['is_check'] = 0;
                $val['is_group'] = 0;
                foreach ($member_api_ids as $m_api_id) {
                    if ($val['api_id'] == $m_api_id) {
                        $val['is_check'] = 1;
                    }
                }
                foreach ($group_api_ids as $g_api_id) {
                    if ($val['api_id'] == $g_api_id) {
                        $val['is_group'] = 1;
                    }
                }
            }
            $info['api_tree'] = list_to_tree($api_list, 'api_id', 'api_pid');
        }

        return $info;
    }

    /**
     * 会员添加
     *
     * @param array $param 会员信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        // 密码
        if (isset($param['password'])) {
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }
        // 默认分组
        if (empty($param['group_ids'])) {
            $param['group_ids'] = GroupService::default_ids();
        }

        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 添加标签
            if (isset($param['tag_ids'])) {
                $model->tags()->saveAll($param['tag_ids']);
            }
            // 添加分组
            if (isset($param['group_ids'])) {
                $model->groups()->saveAll($param['group_ids']);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }

    /**
     * 会员修改
     *
     * @param int|array $ids   会员id
     * @param array     $param 会员信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        // 密码
        if (isset($param['password'])) {
            $param['password'] = password_hash($param['password'], PASSWORD_BCRYPT);
        }

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['tag_ids', 'group_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        $info = $info->append(['tag_ids']);
                        relation_update($info, $info['tag_ids'], $param['tag_ids'], 'tags');
                    }
                    // 修改分组
                    if (isset($param['group_ids'])) {
                        $info = $info->append(['group_ids']);
                        relation_update($info, $info['group_ids'], $param['group_ids'], 'groups');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        MemberCache::upd($ids);

        return $param;
    }

    /**
     * 会员删除
     *
     * @param int|array $ids  会员id
     * @param bool      $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new MemberModel();
        $pk = $model->getPk();

        // 启动事务
        $model::startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除标签
                    $info->tags()->detach();
                    // 删除分组
                    $info->groups()->detach();
                }
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model::commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model::rollback();
        }

        if (isset($errmsg)) {
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

        $account  = $param['account'] ?? '';
        $username = $param['username'] ?? $account;
        $phone    = $param['phone'] ?? $account;
        $email    = $param['email'] ?? $account;
        $password = $param['password'] ?? '';

        $where = [];
        if ($type == 'username') {
            // 通过会员名登录
            $where[] = ['username', '=', $username];
        } else if ($type == 'phone') {
            // 通过手机登录
            $where[] = ['phone', '=', $phone];
        } else if ($type == 'email') {
            // 通过邮箱登录
            $where[] = ['email', '=', $email];
        } else {
            if (Validate::rule('account', 'mobile')->check(['account' => $account])) {
                $where[] = ['phone', '=', $account];
            } else if (Validate::rule('account', 'email')->check(['account' => $account])) {
                $where[] = ['email', '=', $account];
            } else {
                $where[] = ['username', '=', $account];
            }
        }
        $where[] = where_delete();

        $field = $pk . ',username,nickname,phone,email,password,login_num,is_disable';
        $member = $model->field($field)->where($where)->find();
        if (empty($member)) {
            if (empty($type)) {
                $member = $model->field($field)->where('username|phone|email', $account)->where([where_delete()])->find();
            }
            if (empty($member)) {
                exception('账号或密码错误.');
            }
        }
        if ($password) {
            if (!password_verify($password, $member['password'])) {
                exception('账号或密码错误..');
            }
        }

        $member = $member->toArray();
        if ($member['is_disable'] == 1) {
            exception('账号已被禁用，请联系客服');
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
        LogService::add($member_log, SettingService::LOG_TYPE_LOGIN);

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        $data = self::loginField($member);

        return $data;
    }

    /**
     * 会员微信登录注册
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
        $reg_type    = $userinfo['reg_type'];
        $reg_channel = $userinfo['reg_channel'];
        $ip_info     = IpInfoUtils::info($login_ip);

        unset($userinfo['login_ip'], $userinfo['reg_type'], $userinfo['reg_channel']);

        $MemberModel = new MemberModel();
        $MemberPk = $MemberModel->getPk();

        $field = $MemberPk . ',nickname,login_num,is_disable,is_delete';
        if ($unionid) {
            $member = $MemberModel->field($field)->where(where_delete(['unionid', '=', $unionid]))->find();
        } else {
            $member = $MemberModel->field($field)->where(where_delete(['openid', '=', $openid]))->find();
        }

        $errmsg = '';
        // 启动事务
        $MemberModel->startTrans();
        try {
            $setting = SettingService::info();

            if ($member) {
                if ($reg_channel == SettingService::REG_CHANNEL_OFFI && !$setting['is_offi_login']) {
                    exception('系统维护，无法登录：offi');
                } elseif ($reg_channel == SettingService::REG_CHANNEL_MINI && !$setting['is_mini_login']) {
                    exception('系统维护，无法登录：mini');
                }
                $member_id = $member[$MemberPk];
            } else {
                if ($reg_channel == SettingService::REG_CHANNEL_OFFI && !$setting['is_offi_register']) {
                    exception('系统维护，无法注册：offi');
                } elseif ($reg_channel == SettingService::REG_CHANNEL_MINI && !$setting['is_mini_register']) {
                    exception('系统维护，无法注册：mini');
                }
                $insert = $userinfo;
                $insert['create_time'] = $datetime;
                $member_id = 0;
            }

            $member = $MemberModel->field($field)->where(where_delete([$MemberPk, '=', $member_id]))->find();
            if ($member) {
                $member = $member->toArray();
                if ($member['is_disable']) {
                    exception('账号已被禁用');
                }
                if (empty($member['nickname'])) {
                    $update['nickname'] = $userinfo['nickname'];
                }
                $update['login_num']    = $member['login_num'] + 1;
                $update['login_ip']     = $login_ip;
                $update['login_time']   = $datetime;
                $update['login_region'] = $ip_info['region'];
                $MemberModel->where($MemberPk, $member_id)->update($update);
                // 登录日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, SettingService::LOG_TYPE_LOGIN);
            } else {
                $wx_username = md5(uniqid('wx', true));
                if ($reg_channel == SettingService::REG_CHANNEL_OFFI) {
                    $insert['username'] = 'wxoffi' . $wx_username;
                } elseif ($reg_channel == SettingService::REG_CHANNEL_MINI) {
                    $insert['username'] = 'wxmini' . $wx_username;
                } else {
                    $insert['username'] = 'wx' . $wx_username;
                }
                $insert['nickname']     = $userinfo['nickname'] ?: $insert['username'];
                $insert['login_num']    = 1;
                $insert['login_ip']     = $login_ip;
                $insert['login_time']   = $datetime;
                $insert['login_region'] = $ip_info['region'];
                $insert['create_time']  = $datetime;
                $insert['reg_channel']  = $reg_channel;
                $insert['reg_type']     = $reg_type;
                $member = MemberService::add($insert);
                $member_id = $member[$MemberPk];
                // 注册日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, SettingService::LOG_TYPE_REGISTER);
            }

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
        $data = self::loginField($member);

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
        $setting = SettingService::info();
        $fields = ['avatar_url', 'member_id', 'nickname', 'username', 'phone', 'email', 'login_ip', 'login_time', 'login_num', $setting['token_name']];
        foreach ($fields as $field) {
            if ($member[$field] ?? '') {
                $data[$field] = $member[$field];
            }
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
            $phone_where[] = where_delete();
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
     * @Apidoc\Returned("type", type="string", desc="日期类型"),
     *   @Apidoc\Returned("date", type="array", desc="日期范围"),
     *   @Apidoc\Returned("title", type="string", desc="图表title.text"),
     *   @Apidoc\Returned("legend", type="array", desc="图表legend.data"),
     *   @Apidoc\Returned("xAxis", type="array", desc="图表xAxis.data"),
     *   @Apidoc\Returned("series", type="array", desc="图表series")
     * )
     * @return array
     */
    public static function statistic($type = 'month', $date = [], $stat = 'count')
    {
        if (empty($date)) {
            $date = [];
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

        $key = $type . $stat . $sta_date . '_' . $end_date;
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
            $where[] = ['member_id', '>', 0];
            $where[] = ['create_time', '>=', $sta_date . ' 00:00:00'];
            $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
            $where[] = where_delete();

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
                    $where[] = ['member_id', '>', 0];
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
                    $where[] = where_delete();
                    $data[$k]['count'] = $model->where($where)->count();
                }

                MemberCache::set($key, $data, 120);

                return $data;
            } elseif ($stat == 'number') {
                $data['title'] = '数量';
                $add = $total = [];
                // 新增会员
                $adds = $model->field($field)->where($where)->group($group)->select()->column('num', 'date');
                // 会员总数
                foreach ($dates as $k => $v) {
                    $add[$k] = $adds[$v] ?? 0;
                    if ($type == 'month') {
                        $e_t = date('Y-m-d', strtotime('next month -1 day', strtotime(date('Y-m-01', strtotime($v)))));
                    } else {
                        $e_t = $v;
                    }
                    $total[$k] = $model->where(where_delete(['create_time', '<=', $e_t . ' 23:59:59']))->count();
                }
                $series = [
                    ['name' => '总数', 'type' => 'line', 'data' => $total, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => '新增', 'type' => 'line', 'data' => $add, 'label' => ['show' => true, 'position' => 'top']],
                ];
            } elseif ($stat == 'reg_channel') {
                $data['title'] = '注册渠道';
                $series = [];
                $reg_channels = SettingService::reg_channels();
                foreach ($reg_channels as $k => $v) {
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
                $reg_types = SettingService::reg_types();
                foreach ($reg_types as $k => $v) {
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
