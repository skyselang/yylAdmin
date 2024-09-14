<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Config;
use think\facade\Request;
use app\common\cache\member\SettingCache;
use app\common\model\member\SettingModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员设置
 */
class SettingService
{
    /**
     * 会员设置id
     * @var integer
     */
    private static $id = 1;

    /**
     * 公众号/网站，登录/绑定信息缓存键
     */
    const OFFIACC_WEBSITE_KEY = 'offiaccWebsiteLogin:';

    /**
     * 性别：未知
     * @var integer
     */
    const GENDER_UNKNOWN = 0;
    /**
     * 性别：男
     * @var integer
     */
    const GENDER_MAN     = 1;
    /**
     * 性别：女
     * @var integer
     */
    const GENDER_WOMAN   = 2;
    /**
     * 性别数组或名称
     * @param string $gender 性别
     * @return array|string  
     */
    public static function genders($gender = '')
    {
        $genders = [
            self::GENDER_UNKNOWN => '未知',
            self::GENDER_MAN     => '男',
            self::GENDER_WOMAN   => '女',
        ];
        if ($gender !== '') {
            return $genders[$gender] ?? '';
        }
        return $genders;
    }

    /**
     * 平台说明
     * @var string
     */
    const PLATFORM_DESC = '系统：不使用第三方平台（微信、QQ等）授权登录注册，使用账号（用户名、手机、邮箱）密码登录注册；<br>
    微信：使用微信平台的应用（小程序、公众号、移动应用、网站应用等）且使用微信授权登录注册；<br>
    QQ：使用QQ平台的应用（小程序、移动应用、网站应用等）且使用QQ授权登录注册；<br>
    微博：使用微博平台的应用（移动应用、网站应用等）且使用微博授权登录注册。<br>';
    /**
     * 平台：系统
     * @var integer
     */
    const PLATFORM_YA = 10;
    /**
     * 平台：微信
     * @var integer
     */
    const PLATFORM_WX = 20;
    /**
     * 平台：QQ
     * @var integer
     */
    const PLATFORM_QQ = 30;
    /**
     * 平台：微博
     * @var integer
     */
    const PLATFORM_WB = 40;
    /**
     * 平台数组或名称
     * @param string $platform 平台
     * @param bool   $val_lab  是否返回带value、label的数组
     * @return array|string 
     * @Apidoc\Param("platform", type="string", default="10", desc="平台：10系统，20微信，30 QQ，40微博")
     */
    public static function platforms($platform = '', $val_lab = false)
    {
        $platforms = [
            self::PLATFORM_YA => '系统',
            self::PLATFORM_WX => '微信',
            self::PLATFORM_QQ => 'QQ',
            self::PLATFORM_WB => '微博',
        ];
        if ($platform !== '') {
            return $platforms[$platform] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($platforms as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $platforms;
    }

    /**
     * 应用：未知
     * @var integer
     */
    const APP_UNKNOWN    = 0;
    /**
     * 应用：系统后台
     * @var integer
     */
    const APP_YA_ADMIN   = 10;
    /**
     * 应用：系统小程序
     * @var integer
     */
    const APP_YA_MINIAPP = 11;
    /**
     * 应用：系统公众号
     * @var integer
     */
    const APP_YA_OFFIACC = 12;
    /**
     * 应用：系统网站应用
     * @var integer
     */
    const APP_YA_WEBSITE = 13;
    /**
     * 应用：系统移动应用
     * @var integer
     */
    const APP_YA_MOBILE  = 14;

    /**
     * 应用：微信小程序
     * @var integer
     */
    const APP_WX_MINIAPP = 21;
    /**
     * 应用：微信公众号
     * @var integer
     */
    const APP_WX_OFFIACC = 22;
    /**
     * 应用：微信网站应用
     * @var integer
     */
    const APP_WX_WEBSITE = 23;
    /**
     * 应用：微信移动应用
     * @var integer
     */
    const APP_WX_MOBILE  = 24;

    /**
     * 应用：QQ小程序
     * @var integer
     */
    const APP_QQ_MINIAPP = 31;
    /**
     * 应用：QQ网站应用
     * @var integer
     */
    const APP_QQ_WEBSITE = 33;
    /**
     * 应用：QQ移动应用
     * @var integer
     */
    const APP_QQ_MOBILE  = 34;

    /**
     * 应用：微博网站应用
     * @var integer
     */
    const APP_WB_WEBSITE = 43;

    /**
     * 应用数组或名称
     * @param string $application 应用
     * @param bool   $val_lab     是否返回带value、label的数组
     * @return array|string 
     * @Apidoc\Param("application", type="string", default="0", desc="应用：0未知，10系统后台，11系统小程序，12系统公众号，13系统网站应用，14系统移动应用；21微信小程序，22微信公众号，23微信网页应用，24微信移动应用；31QQ小程序，33QQ网站应用，34QQ移动应用；43微博网站应用")
     */
    public static function applications($application = '', $val_lab = false)
    {
        $applications = [
            self::APP_UNKNOWN    => '未知',
            self::APP_YA_ADMIN   => '系统后台',
            self::APP_YA_MINIAPP => '系统小程序',
            self::APP_YA_OFFIACC => '系统公众号',
            self::APP_YA_WEBSITE => '系统网站应用',
            self::APP_YA_MOBILE  => '系统移动应用',
            self::APP_WX_MINIAPP => '微信小程序',
            self::APP_WX_OFFIACC => '微信公众号',
            self::APP_WX_WEBSITE => '微信网站应用',
            self::APP_WX_MOBILE  => '微信移动应用',
            self::APP_QQ_MINIAPP => 'QQ小程序',
            self::APP_QQ_WEBSITE => 'QQ网站应用',
            self::APP_QQ_MOBILE  => 'QQ移动应用',
            self::APP_WB_WEBSITE => '微博网站应用',
        ];
        if ($application !== '') {
            return $applications[$application] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($applications as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $applications;
    }

    /**
     * 日志类型：注册
     * @var integer
     */
    const LOG_TYPE_REGISTER  = 0;
    /**
     * 日志类型：登录
     * @var integer
     */
    const LOG_TYPE_LOGIN     = 1;
    /**
     * 日志类型：操作
     * @var integer
     */
    const LOG_TYPE_OPERATION = 2;
    /**
     * 日志类型：退出
     * @var integer
     */
    const LOG_TYPE_LOGOUT    = 3;
    /**
     * 日志类型数组或名称
     * @param string $log_type 日志类型
     * @return array|string 
     */
    public static function logTypes($log_type = '')
    {
        $log_types = [
            self::LOG_TYPE_REGISTER  => '注册',
            self::LOG_TYPE_LOGIN     => '登录',
            self::LOG_TYPE_OPERATION => '操作',
            self::LOG_TYPE_LOGOUT    => '退出',
        ];
        if ($log_type !== '') {
            return $log_types[$log_type] ?? '';
        }
        return $log_types;
    }

    /**
     * 会员设置信息
     * @param string $fields  返回字段，逗号隔开，默认所有
     * @param string $without 排除字段，逗号隔开，默认不排除
     * @Apidoc\Returned("default_avatar_url", type="string", desc="会员默认头像链接")
     * @Apidoc\Returned("token_type", type="string", require=false, default="", desc="token方式")
     * @Apidoc\Returned("token_name", type="string", require=false, default="", desc="token名称")
     * @return array
     */
    public static function info($fields = '', $withouts = '')
    {
        $id   = self::$id;
        $type = Request::isCli() ? 'cli' : 'cgi';
        $key  = $id . $type;

        $info = SettingCache::get($key);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['token_key']   = uniqids();
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }

            // 命令行无法获取域名
            $append = [];
            $hidden = [];
            if ($type == 'cgi') {
                $append = array_merge($append, ['default_avatar_url']);
                $hidden = array_merge($hidden, ['defaultavatar']);
            }
            $info = $info->append($append)->hidden($hidden)->toArray();

            $info['token_type'] = Config::get('api.token_type');
            $info['token_name'] = Config::get('api.token_name');
            $info['token_exps'] = $info['token_exp'] * 3600;

            SettingCache::set($key, $info);
        }

        if ($withouts) {
            $withouts = explode(',', $withouts);
            foreach ($withouts as $without) {
                $without = trim($without);
                unset($info[$without]);
            }
        }

        if ($fields) {
            $data = [];
            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                $field = trim($field);
                if (isset($info[$field])) {
                    $data[$field] = $info[$field];
                }
            }
            return $data;
        }

        return $info;
    }

    /**
     * 会员设置修改
     *
     * @param array $param 设置信息
     *
     * @return array|Exception
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $id = self::$id;

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $info = $model->find($id);
        $res = $info->save($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::clear();

        return $param;
    }

    /**
     * 获取平台
     *
     * @param  int $application 应用
     * @return int
     */
    public static function platform($application)
    {
        $app_ya = [self::APP_YA_MINIAPP, self::APP_YA_OFFIACC, self::APP_YA_WEBSITE, self::APP_YA_MOBILE];
        if (in_array($application, $app_ya)) {
            return self::PLATFORM_YA;
        }

        $app_wx = [self::APP_WX_MINIAPP, self::APP_WX_OFFIACC, self::APP_WX_WEBSITE, self::APP_WX_MOBILE];
        if (in_array($application, $app_wx)) {
            return self::PLATFORM_WX;
        }

        $app_qq = [self::APP_QQ_MINIAPP, self::APP_QQ_WEBSITE, self::APP_QQ_MOBILE];
        if (in_array($application, $app_qq)) {
            return self::PLATFORM_QQ;
        }

        $app_wb = [self::APP_WB_WEBSITE];
        if (in_array($application, $app_wb)) {
            return self::PLATFORM_WB;
        }

        return self::PLATFORM_YA;
    }
}
