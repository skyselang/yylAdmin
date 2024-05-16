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
use app\common\service\member\SettingService;
use app\common\service\member\ThirdService;
use app\common\service\utils\Utils;
use app\common\service\utils\RetCodeUtils;
use app\common\model\member\MemberModel;
use app\common\model\member\ThirdModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员管理
 */
class MemberService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'member_id/d' => '',
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
        'group_ids/a' => [],
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
            $field = $group . ',avatar_id,headimgurl,nickname,username,phone,email,sort,is_super,is_disable,create_time';
        }
        if (empty($order)) {
            $order = [$group => 'desc'];
        }

        $model = $model->alias('m');
        foreach ($where as $wk => $wv) {
            if ($wv[0] == 'tag_ids' && is_array($wv[2])) {
                $model = $model->join('member_attributes t', 'm.member_id=t.member_id')->where('t.tag_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
            if ($wv[0] == 'group_ids' && is_array($wv[2])) {
                $model = $model->join('member_attributes g', 'm.member_id=g.member_id')->where('g.group_id', $wv[1], $wv[2]);
                unset($where[$wk]);
            }
        }
        $where = array_values($where);

        $with     = ['tags', 'groups'];
        $append   = ['tag_names', 'group_names'];
        $hidden   = ['tags', 'groups'];
        $field_no = [];
        if (strpos($field, 'avatar_id') !== false) {
            $with[]   = $hidden[] = 'avatar';
            $append[] = 'avatar_url';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $model->where($where)->group($group)->count();
        $pages = 0;
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->group($group)->select()->toArray();

        $genders      = SettingService::genders();
        $platforms    = SettingService::platforms();
        $applications = SettingService::applications();

        return compact('count', 'pages', 'page', 'limit', 'list', 'genders', 'platforms', 'applications');
    }

    /**
     * 会员信息
     *
     * @param int  $id    会员id
     * @param bool $exce  不存在是否抛出异常
     * @param bool $group 是否返回分组信息
     * @param bool $third 是否返回第三方账号信息
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true, $group = false, $third = false)
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
                ->append(['avatar_url', 'tag_ids', 'tag_names', 'group_ids', 'group_names', 'gender_name', 'platform_name', 'application_name'])
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
            sort($api_ids);
            sort($api_urls);
            $info['api_ids']  = $api_ids;
            $info['api_urls'] = $api_urls;

            MemberCache::set($id, $info);
        }

        // 分组（接口权限）
        if ($group) {
            $member_api_ids = $info['api_ids'] ?? [];
            $group_api_ids  = GroupService::api_ids($info['group_ids'], where_disdel());
            $api_field      = 'api_id,api_pid,api_name,api_url,is_unlogin,is_unauth';
            $api_lists      = ApiService::list('list', [where_delete()], [], $api_field);
            foreach ($api_lists as &$val) {
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
            $info['api_tree'] = list_to_tree($api_lists, 'api_id', 'api_pid');

            $unlogin_api_ids = ApiService::unloginList('id');
            $unauth_api_ids  = ApiService::unauthList('id');
            $auth_api_ids    = array_merge($member_api_ids, $group_api_ids, $unlogin_api_ids, $unauth_api_ids);
            $auth_api_where  = [['api_id', 'in', $auth_api_ids], where_disdel(), where_delete()];
            $auth_api_list   = ApiService::list('list', $auth_api_where, [], $api_field);
            $info['auth_api_list'] = $auth_api_list;
            $info['auth_api_urls'] = array_values(array_filter(array_column($auth_api_list, 'api_url')));
            sort($info['auth_api_urls']);
        }

        // 第三方账号
        if ($third) {
            $MemberModel = new MemberModel();
            $MemberPk = $MemberModel->getPk();
            $third_where[] = [$MemberPk, '=', $info[$MemberPk]];
            $third_where[] = where_delete();
            $third_field = 'third_id,member_id,platform,application,headimgurl,nickname,is_disable,login_time,create_time';
            $info['thirds'] = ThirdService::list($third_where, 0, 0, [], $third_field)['list'] ?? [];
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

        MemberCache::del($ids);

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

        $Third = new ThirdModel();

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
                // 删除三方账号
                $Third->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
                $Third->where($pk, 'in', $ids)->update($update);
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
        MemberCache::delToken($ids);

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
            // 通过用户名登录
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

        $ip_info   = Utils::ipInfo();
        $member_id = $member[$pk];

        // 登录信息
        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_num']    = $member['login_num'] + 1;
        $update['login_time']   = datetime();
        $model->where($pk, $member_id)->update($update);

        // 登录日志
        $member_log[$pk]           = $member_id;
        $member_log['platform']    = $param['platform'] ?? SettingService::PLATFORM_YA;
        $member_log['application'] = $param['application'] ?? SettingService::APP_UNKNOWN;
        LogService::add($member_log, SettingService::LOG_TYPE_LOGIN);

        // 会员信息
        MemberCache::del($member_id);
        $member = MemberService::info($member_id);
        // 返回字段
        $member['platform']    = $member_log['platform'];
        $member['application'] = $member_log['application'];
        $data = self::loginField($member);

        return $data;
    }

    /**
     * 会员登录返回字段
     *
     * @param array $member 会员信息
     *
     * @return array
     */
    public static function loginField($member)
    {
        $data = [];
        $setting = SettingService::info();
        $token_name = $setting['token_name'];
        $member[$token_name] = self::token($member);
        $fields = ['avatar_url', 'member_id', 'nickname', 'username', 'login_ip', 'login_time', 'login_num', $token_name];
        foreach ($fields as $field) {
            if (isset($member[$field])) {
                $data[$field] = $member[$field];
            }
        }

        return $data;
    }

    /**
     * 会员第三方账号登录
     *
     * @param array $third_info 第三方账号信息
     * openid、platform、application、headimgurl、nickname、unionid
     * 
     * @return array
     */
    public static function thirdLogin($third_info)
    {
        $register    = $third_info['register'] ?? 0;
        $unionid     = $third_info['unionid'] ?? '';
        $openid      = $third_info['openid'] ?? '';
        $platform    = $third_info['platform'];
        $application = $third_info['application'];
        $setting     = SettingService::info();
        $ip_info     = Utils::ipInfo();
        $login_ip    = $ip_info['ip'];
        $datetime    = datetime();


        if (empty($openid)) {
            exception('登录失败：get openid fail');
        }

        $applications = [
            SettingService::APP_WX_MINIAPP,
            SettingService::APP_WX_OFFIACC,
            SettingService::APP_WX_WEBSITE,
            SettingService::APP_WX_MOBILE,
            SettingService::APP_QQ_MINIAPP,
            SettingService::APP_QQ_WEBSITE,
            SettingService::APP_QQ_MOBILE,
            SettingService::APP_WB_WEBSITE,
        ];
        if (!in_array($application, $applications)) {
            exception('登录错误：application absent ' . $application);
        }

        $ThirdModel = new ThirdModel();
        $ThirdPk    = $ThirdModel->getPk();

        $MemberModel = new MemberModel();
        $MemberPk    = $MemberModel->getPk();

        $third_field = $ThirdPk . ',member_id,platform,application,openid,unionid,login_num,is_disable';
        if ($unionid) {
            $third_u_where = [['platform', '=', $platform], ['unionid', '=', $unionid], where_delete()];
            $third_unionid = $ThirdModel->field($third_field)->where($third_u_where)->find();
        }
        $third_o_where = [['application', '=', $application], ['openid', '=', $openid], where_delete()];
        $third_openid  = $ThirdModel->field($third_field)->where($third_o_where)->find();

        $errmsg_login    = '系统维护，无法登录';
        $errmsg_register = '系统维护，无法注册';
        if ($third_unionid ?? [] || $third_openid) {
            if ($application == SettingService::APP_WX_MINIAPP && !$setting['wx_miniapp_login']) {
                exception($errmsg_login . '：wx miniapp');
            } elseif ($application == SettingService::APP_WX_OFFIACC && !$setting['wx_offiacc_login']) {
                exception($errmsg_login . '：wx offiacc');
            } elseif ($application == SettingService::APP_WX_WEBSITE && !$setting['wx_website_login']) {
                exception($errmsg_login . '：wx website');
            } elseif ($application == SettingService::APP_WX_MOBILE && !$setting['wx_mobile_login']) {
                exception($errmsg_login . '：wx mobile');
            } elseif ($application == SettingService::APP_QQ_MINIAPP && !$setting['qq_miniapp_login']) {
                exception($errmsg_login . '：qq miniapp');
            } elseif ($application == SettingService::APP_QQ_WEBSITE && !$setting['qq_website_login']) {
                exception($errmsg_login . '：qq website');
            } elseif ($application == SettingService::APP_QQ_MOBILE && !$setting['qq_mobile_login']) {
                exception($errmsg_login . '：qq mobile');
            } elseif ($application == SettingService::APP_WB_WEBSITE && !$setting['wb_website_login']) {
                exception($errmsg_login . '：wb website');
            }
            $third_u_id = $third_unionid[$ThirdPk] ?? 0;
            $third_o_id = $third_openid[$ThirdPk] ?? 0;
            $member_id  = $third_unionid[$MemberPk] ?? $third_openid[$MemberPk] ?? 0;
            if ($third_unionid['is_disable'] ?? 0 || $third_openid['is_disable'] ?? 0) {
                exception($errmsg_login . '：第三方账号已禁用');
            }
        } else {
            if ($application == SettingService::APP_WX_MINIAPP && !$setting['wx_miniapp_register']) {
                exception($errmsg_register . '：wx miniapp');
            } elseif ($application == SettingService::APP_WX_OFFIACC && !$setting['wx_offiacc_register']) {
                exception($errmsg_register . '：wx offiacc');
            } elseif ($application == SettingService::APP_WX_WEBSITE && !$setting['wx_website_register']) {
                exception($errmsg_register . '：wx website');
            } elseif ($application == SettingService::APP_WX_MOBILE && !$setting['wx_mobile_register']) {
                exception($errmsg_register . '：wx mobile');
            } elseif ($application == SettingService::APP_QQ_MINIAPP && !$setting['qq_miniapp_register']) {
                exception($errmsg_register . '：qq miniapp');
            } elseif ($application == SettingService::APP_QQ_WEBSITE && !$setting['qq_website_register']) {
                exception($errmsg_register . '：qq website');
            } elseif ($application == SettingService::APP_QQ_MOBILE && !$setting['qq_mobile_register']) {
                exception($errmsg_register . '：qq mobile');
            } elseif ($application == SettingService::APP_WB_WEBSITE && !$setting['wb_website_register']) {
                exception($errmsg_register . '：wb website');
            }
            $third_u_id = 0;
            $third_o_id = 0;
            $member_id  = 0;

            if ($register == 0) {
                exception('未注册', RetCodeUtils::THIRD_UNREGISTERED);
            }
        }

        // 启动事务
        $ThirdModel->startTrans();
        try {
            $member_field = $MemberPk . ',headimgurl,nickname,login_num';
            $member_where = where_delete([$MemberPk, '=', $member_id]);
            $member = $MemberModel->field($member_field)->where($member_where)->find();
            if ($member) {
                if (isset($third_info['headimgurl'])) {
                    $member_update['headimgurl'] = $third_info['headimgurl'];
                }
                if (empty($member['nickname']) && isset($third_info['nickname'])) {
                    $member_update['nickname'] = $third_info['nickname'];
                }
                $member_update['login_num']    = $member['login_num'] + 1;
                $member_update['login_ip']     = $login_ip;
                $member_update['login_time']   = $datetime;
                $member_update['login_region'] = $ip_info['region'];
                $MemberModel->where($MemberPk, $member_id)->update($member_update);
                // 登录日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, SettingService::LOG_TYPE_LOGIN);
            } else {
                $third_username = md5(uniqid('third', true));
                if ($application == SettingService::APP_WX_MINIAPP) {
                    $member_insert['username'] = 'wxminiapp' . $third_username;
                } elseif ($application == SettingService::APP_WX_OFFIACC) {
                    $member_insert['username'] = 'wxoffiacc' . $third_username;
                } elseif ($application == SettingService::APP_WX_WEBSITE) {
                    $member_insert['username'] = 'wxwebsite' . $third_username;
                } elseif ($application == SettingService::APP_WX_MOBILE) {
                    $member_insert['username'] = 'wxmobile' . $third_username;
                } elseif ($application == SettingService::APP_QQ_MINIAPP) {
                    $member_insert['username'] = 'qqminiapp' . $third_username;
                } elseif ($application == SettingService::APP_QQ_WEBSITE) {
                    $member_insert['username'] = 'qqwebsite' . $third_username;
                } elseif ($application == SettingService::APP_QQ_MOBILE) {
                    $member_insert['username'] = 'qqmobile' . $third_username;
                } elseif ($application == SettingService::APP_WB_WEBSITE) {
                    $member_insert['username'] = 'wbwebsite' . $third_username;
                }
                if (isset($third_info['headimgurl'])) {
                    $member_insert['headimgurl'] = $third_info['headimgurl'];
                }
                if (isset($third_info['nickname'])) {
                    $member_insert['nickname'] = $third_info['nickname'];
                } else {
                    $member_insert['nickname'] = $member_insert['username'] ?? $third_username;
                }
                $member_insert['platform']     = $platform;
                $member_insert['application']  = $application;
                $member_insert['login_num']    = 1;
                $member_insert['login_ip']     = $login_ip;
                $member_insert['login_time']   = $datetime;
                $member_insert['login_region'] = $ip_info['region'];
                $member_add = MemberService::add($member_insert);
                $member_id  = $member_add[$MemberPk];
                // 注册日志
                $member_log[$MemberPk] = $member_id;
                LogService::add($member_log, SettingService::LOG_TYPE_REGISTER);
            }

            $third_save['member_id']    = $member_id;
            $third_save['openid']       = $openid;
            $third_save['login_ip']     = $login_ip;
            $third_save['login_time']   = $datetime;
            $third_save['login_region'] = $ip_info['region'];
            if ($unionid) {
                $third_save['unionid'] = $unionid;
            }
            if (isset($third_info['headimgurl'])) {
                $third_save['headimgurl'] = $third_info['headimgurl'];
            }
            if (isset($third_info['nickname'])) {
                $third_save['nickname'] = $third_info['nickname'];
            }

            if ($third_u_id && $third_u_id != $third_o_id) {
                $ThirdModel->where($ThirdPk, $third_u_id)->update(['update_time' => $datetime]);
            }
            if ($third_o_id) {
                $third_o_update = $third_save;
                $third_o_update['login_num'] = $third_openid['login_num'] + 1;
                $ThirdModel->where($ThirdPk, $third_o_id)->update($third_o_update);
            } else {
                $third_o_insert = $third_save;
                $third_o_insert['login_num']    = 1;
                $third_o_insert['platform']     = $platform;
                $third_o_insert['application']  = $application;
                $third_o_insert['create_time']  = $datetime;
                $ThirdModel->save($third_o_insert);
                $third_o_id = $ThirdModel->$ThirdPk;
            }

            // 提交事务
            $ThirdModel->commit();
        } catch (\Exception $e) {
            $errmsg = '登录失败：' . $e->getMessage();
            // 回滚事务
            $ThirdModel->rollback();
        }

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        // 会员信息
        MemberCache::del($member_id);
        $member = self::info($member_id);
        $data   = self::loginField($member);
        $data['member_id']   = $member_id;
        $data['third_id']    = $third_o_id;

        return $data;
    }

    /**
     * 会员第三方账号绑定
     *
     * @param array $third_info 第三方账号信息
     * member_id、openid、platform、application、headimgurl、nickname、unionid
     * 
     * @return array
     */
    public static function thirdBind($third_info)
    {
        $member_id   = $third_info['member_id'];
        $unionid     = $third_info['unionid'] ?? '';
        $openid      = $third_info['openid'] ?? '';
        $platform    = $third_info['platform'];
        $application = $third_info['application'];
        $setting     = SettingService::info();
        $datetime    = datetime();

        if (empty($member_id)) {
            exception('绑定失败：member_id is null');
        }
        if (empty($openid)) {
            exception('绑定失败：get openid fail');
        }

        $applications = [
            SettingService::APP_WX_MINIAPP,
            SettingService::APP_WX_OFFIACC,
            SettingService::APP_WX_WEBSITE,
            SettingService::APP_WX_MOBILE,
            SettingService::APP_QQ_MINIAPP,
            SettingService::APP_QQ_WEBSITE,
            SettingService::APP_QQ_MOBILE,
            SettingService::APP_WB_WEBSITE,
        ];
        if (!in_array($application, $applications)) {
            exception('绑定错误：application absent ' . $application);
        }

        $ThirdModel = new ThirdModel();
        $ThirdPk    = $ThirdModel->getPk();

        $MemberModel = new MemberModel();
        $MemberPk    = $MemberModel->getPk();

        $third_field = $ThirdPk . ',member_id,platform,application,openid,unionid,login_num';
        if ($unionid) {
            $third_u_where = [['unionid', '=', $unionid], ['platform', '=', $platform], where_delete()];
            $third_unionid = $ThirdModel->field($third_field)->where($third_u_where)->find();
        }
        $third_o_where = [['openid', '=', $openid], ['application', '=', $application], where_delete()];
        $third_openid  = $ThirdModel->field($third_field)->where($third_o_where)->find();

        $errmsg_bind = '功能维护，无法绑定';
        if ($application == SettingService::APP_WX_MINIAPP && !$setting['wx_miniapp_bind']) {
            exception($errmsg_bind . '：wx miniapp');
        } elseif ($application == SettingService::APP_WX_OFFIACC && !$setting['wx_offiacc_bind']) {
            exception($errmsg_bind . '：wx offiacc');
        } elseif ($application == SettingService::APP_WX_WEBSITE && !$setting['wx_website_bind']) {
            exception($errmsg_bind . '：wx website');
        } elseif ($application == SettingService::APP_WX_MOBILE && !$setting['wx_mobile_bind']) {
            exception($errmsg_bind . '：wx mobile');
        } elseif ($application == SettingService::APP_QQ_MINIAPP && !$setting['qq_miniapp_bind']) {
            exception($errmsg_bind . '：qq miniapp');
        } elseif ($application == SettingService::APP_QQ_WEBSITE && !$setting['qq_website_bind']) {
            exception($errmsg_bind . '：qq website');
        } elseif ($application == SettingService::APP_QQ_MOBILE && !$setting['qq_mobile_bind']) {
            exception($errmsg_bind . '：qq mobile');
        } elseif ($application == SettingService::APP_WB_WEBSITE && !$setting['wb_website_bind']) {
            exception($errmsg_bind . '：wb website');
        }
        $third_u_id = $third_unionid[$ThirdPk] ?? 0;
        $third_o_id = $third_openid[$ThirdPk] ?? 0;
        $third_m_id = $third_unionid[$MemberPk] ?? $third_openid[$MemberPk] ?? 0;
        if ($third_m_id && $third_m_id != $member_id) {
            exception('绑定失败：已被其它会员绑定');
        }

        // 启动事务
        $ThirdModel->startTrans();
        try {
            $member_field = $MemberPk . ',headimgurl,nickname,login_num';
            $member_where = where_delete([$MemberPk, '=', $member_id]);
            $member = $MemberModel->field($member_field)->where($member_where)->find();
            if (isset($third_info['headimgurl'])) {
                $member_update['headimgurl'] = $third_info['headimgurl'];
            }
            if (empty($member['nickname']) && isset($third_info['nickname'])) {
                $member_update['nickname'] = $third_info['nickname'];
            }
            if ($member_update ?? []) {
                $MemberModel->where($MemberPk, $member_id)->update($member_update);
            }

            $third_save['member_id'] = $member_id;
            $third_save['openid']    = $openid;
            if ($unionid) {
                $third_save['unionid'] = $unionid;
            }
            if (isset($third_info['headimgurl'])) {
                $third_save['headimgurl'] = $third_info['headimgurl'];
            }
            if (isset($third_info['nickname'])) {
                $third_save['nickname'] = $third_info['nickname'];
            }

            if ($third_u_id && $third_u_id != $third_o_id) {
                $ThirdModel->where($ThirdPk, $third_u_id)->update(['update_time' => $datetime]);
            }
            if ($third_o_id) {
                $third_o_update = $third_save;
                $ThirdModel->where($ThirdPk, $third_o_id)->update($third_o_update);
            } else {
                $third_o_insert = $third_save;
                $third_o_insert['platform']    = $platform;
                $third_o_insert['application'] = $application;
                $third_o_insert['create_time'] = $datetime;
                $ThirdModel->save($third_o_insert);
                $third_o_id = $ThirdModel->$ThirdPk;
            }

            // 提交事务
            $ThirdModel->commit();
        } catch (\Exception $e) {
            $errmsg = '绑定失败：' . $e->getMessage();
            // 回滚事务
            $ThirdModel->rollback();
        }

        if ($errmsg ?? '') {
            exception($errmsg);
        }

        // 会员信息
        $token_name = $setting['token_name'];
        $data[$token_name] = MemberCache::getToken($member_id);
        $data['member_id'] = $member_id;
        $data['third_id']  = $third_o_id;

        return $data;
    }

    /**
     * 会员第三方账号解绑
     *
     * @param  int $third_id  第三方账号id
     * @param  int $member_id 会员id
     *
     * @return bool|Exception
     */
    public static function thirdUnbind($third_id, $member_id = 0)
    {
        $ThirdModel = new ThirdModel();
        $ThirdPk = $ThirdModel->getPk();

        $MemberModel = new MemberModel();
        $MemberPk = $MemberModel->getPk();

        $third = $ThirdModel->find($third_id);
        if (empty($third) || $third['is_delete']) {
            exception('第三方账号不存在：' . $third_id);
        }
        if ($member_id && $third[$MemberPk] != $member_id) {
            exception('解绑失败：非本会员绑定 ' . $third_id);
        }
        if ($third['is_delete']) {
            exception('第三方账号已解绑：' . $third_id);
        }
        if (empty($member_id)) {
            $member_id = $third[$MemberPk];
        }

        $member = $MemberModel->find($member_id);
        $third_where = [where_delete(), [$MemberPk, '=', $third[$MemberPk]]];
        $third_count = $ThirdModel->where($third_where)->count();
        if (empty($member['password']) && $third_count == 1) {
            exception('无法解绑：会员密码未设置且仅绑定了一个第三方账号');
        }

        return $ThirdModel->where($ThirdPk, $third_id)->update(delete_update());
    }

    /**
     * 会员token
     *
     * @param  array $member 会员信息
     *
     * @return string
     */
    public static function token($member)
    {
        $token     = TokenService::create($member);
        $setting   = SettingService::info();
        $token_exp = $setting['token_exp'] * 3600;
        MemberCache::setToken($member['member_id'], $token, $token_exp);
        return $token;
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
        MemberCache::delToken($id);

        return $update;
    }

    /**
     * 会员统计
     *
     * @param string $type 日期类型：day，month
     * @param array  $date 日期范围：[开始日期，结束日期]
     * @param string $stat 统计类型：count总计，number数量，platform平台，application应用
     * @Apidoc\Returned("type", type="string", desc="日期类型")
     * @Apidoc\Returned("date", type="array", desc="日期范围")
     * @Apidoc\Returned("title", type="string", desc="图表title.text")
     * @Apidoc\Returned("legend", type="array", desc="图表legend.data")
     * @Apidoc\Returned("xAxis", type="array", desc="图表xAxis.data")
     * @Apidoc\Returned("series", type="array", desc="图表series")
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

        $key  = $type . $stat . $sta_date . '_' . $end_date . lang_get();
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
                    ['date' => 'total', 'name' => lang('member.total'), 'title' => lang('member.total'), 'count' => 0],
                    ['date' => 'online', 'name' => lang('member.online'), 'title' => lang('member.number'), 'count' => 0],
                    ['date' => 'today', 'name' => lang('member.today'), 'title' => lang('member.added'), 'count' => 0],
                    ['date' => 'yesterday', 'name' => lang('member.yesterday'), 'title' => lang('member.added'), 'count' => 0],
                    ['date' => 'thisweek', 'name' => lang('member.this week'), 'title' => lang('member.added'), 'count' => 0],
                    ['date' => 'lastweek', 'name' => lang('member.last week'), 'title' => lang('member.added'), 'count' => 0],
                    ['date' => 'thismonth', 'name' => lang('member.this month'), 'title' => lang('member.added'), 'count' => 0],
                    ['date' => 'lastmonth', 'name' => lang('member.last month'), 'title' => lang('member.added'), 'count' => 0],
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
                $data['title'] = lang('member.number');
                $data['selected'] = [lang('member.total') => false];
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
                    ['name' => lang('member.total'), 'type' => 'line', 'data' => $total, 'label' => ['show' => true, 'position' => 'top']],
                    ['name' => lang('member.added'), 'type' => 'line', 'data' => $add, 'label' => ['show' => true, 'position' => 'top']],
                ];
            } elseif ($stat == 'application') {
                $data['title'] = lang('member.application');
                $data['selected'] = [];
                $series = [];
                $applications = SettingService::applications();
                foreach ($applications as $k => $v) {
                    $series[] = ['name' => $v, 'application' => $k, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']];
                }
                foreach ($series as $k => $v) {
                    $series_data = $model
                        ->field($field)
                        ->where($where)
                        ->where('application', $v['application'])
                        ->group($group)
                        ->select()
                        ->column('num', 'date');
                    foreach ($dates as $kx => $vx) {
                        $series[$k]['data'][$kx] = $series_data[$vx] ?? 0;
                    }
                }
            } elseif ($stat == 'platform') {
                $data['title'] = lang('member.platform');
                $data['selected'] = [];
                $series = [];
                $platforms = SettingService::platforms();
                foreach ($platforms as $k => $v) {
                    $series[] = ['name' => $v, 'platform' => $k, 'type' => 'line', 'data' => [], 'label' => ['show' => true, 'position' => 'top']];
                }
                foreach ($series as $k => $v) {
                    $series_data = $model
                        ->field($field)
                        ->where($where)
                        ->where('platform', $v['platform'])
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
