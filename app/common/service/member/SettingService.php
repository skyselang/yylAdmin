<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\member\SettingCache as Cache;
use app\common\model\member\SettingModel as Model;
use app\common\utils\CaptchaUtils;
use app\common\utils\AjCaptchaUtils;

/**
 * 会员设置
 */
class SettingService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("captcha_modes", type="array", desc="验证码方式"),
     *   @Apidoc\Returned("captcha_strs", type="array", desc="字符验证码"),
     *   @Apidoc\Returned("captcha_ajs", type="array", desc="行为验证码"),
     * })
     */
    public static function basedata($exp = false)
    {
        $exps    = $exp ? where_exps() : [];
        $modes   = [['value' => 1, 'label' => lang('字符')], ['value' => 2, 'label' => lang('行为')]];
        $typestr = CaptchaUtils::types();
        $typeaj  = AjCaptchaUtils::types();

        return ['exps' => $exps, 'captcha_modes' => $modes, 'captcha_strs' => $typestr, 'captcha_ajs' => $typeaj];
    }

    /**
     * 会员设置id
     */
    private static $id = 1;

    /**
     * 公众号/网站，登录/绑定，信息缓存键
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
     * @param string $gender  性别
     * @param bool   $val_lab 是否返回带value、label下标的数组
     */
    public static function genders($gender = '', $val_lab = false)
    {
        $genders = [
            self::GENDER_UNKNOWN => lang('未知'),
            self::GENDER_MAN     => lang('男'),
            self::GENDER_WOMAN   => lang('女'),
        ];
        if ($gender !== '') {
            return $genders[$gender] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($genders as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
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
     * @param bool   $val_lab  是否返回带value、label下标的数组
     * @Apidoc\Param("platform", type="string", default="10", desc="平台：10系统，20微信，30 QQ，40微博")
     */
    public static function platforms($platform = '', $val_lab = false)
    {
        $platforms = [
            self::PLATFORM_YA => lang('系统'),
            self::PLATFORM_WX => lang('微信'),
            self::PLATFORM_QQ => lang('QQ'),
            self::PLATFORM_WB => lang('微博'),
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
     * 获取平台
     * @param int $application 应用
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
     * @param bool   $val_lab     是否返回带value、label下标的数组
     * @Apidoc\Param("application", type="string", default="0", desc="应用：0未知，10系统后台，11系统小程序，12系统公众号，13系统网站应用，14系统移动应用；21微信小程序，22微信公众号，23微信网页应用，24微信移动应用；31QQ小程序，33QQ网站应用，34QQ移动应用；43微博网站应用")
     */
    public static function applications($application = '', $val_lab = false)
    {
        $applications = [
            self::APP_UNKNOWN    => lang('未知'),
            self::APP_YA_ADMIN   => lang('后台', ['name' => lang('系统')]),
            self::APP_YA_MINIAPP => lang('小程序', ['name' => lang('系统')]),
            self::APP_YA_OFFIACC => lang('公众号', ['name' => lang('系统')]),
            self::APP_YA_WEBSITE => lang('网站应用', ['name' => lang('系统')]),
            self::APP_YA_MOBILE  => lang('移动应用', ['name' => lang('系统')]),
            self::APP_WX_MINIAPP => lang('小程序', ['name' => lang('微信')]),
            self::APP_WX_OFFIACC => lang('公众号', ['name' => lang('微信')]),
            self::APP_WX_WEBSITE => lang('网站应用', ['name' => lang('微信')]),
            self::APP_WX_MOBILE  => lang('移动应用', ['name' => lang('微信')]),
            self::APP_QQ_MINIAPP => lang('小程序', ['name' => lang('QQ')]),
            self::APP_QQ_WEBSITE => lang('网站应用', ['name' => lang('QQ')]),
            self::APP_QQ_MOBILE  => lang('移动应用', ['name' => lang('QQ')]),
            self::APP_WB_WEBSITE => lang('网站应用', ['name' => lang('微博')]),
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
     * @param bool   $val_lab  是否返回带value、label下标的数组
     */
    public static function logTypes($log_type = '', $val_lab = false)
    {
        $log_types = [
            self::LOG_TYPE_REGISTER  => lang('注册'),
            self::LOG_TYPE_LOGIN     => lang('登录'),
            self::LOG_TYPE_OPERATION => lang('操作'),
            self::LOG_TYPE_LOGOUT    => lang('退出'),
        ];
        if ($log_type !== '') {
            return $log_types[$log_type] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($log_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }

        return $log_types;
    }

    /**
     * 会员设置信息
     * @param string $fields  返回字段，逗号隔开，默认所有
     * @param string $without 排除字段，逗号隔开，默认不排除
     * @return array
     * @Apidoc\Param(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getDefaultAvatarUrlAttr"}, field="default_avatar_url")
     * @Apidoc\Returned("token_type", type="string", require=false, default="", desc="token方式")
     * @Apidoc\Returned("token_name", type="string", require=false, default="", desc="token名称")
     */
    public static function info($fields = '', $withouts = '')
    {
        $id   = self::$id;
        $type = request()->isCli() ? 'cli' : 'cgi';
        $key  = $id . $type;

        $cache = self::cache();
        $info  = $cache->get($key);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['token_key']   = random_string(32);
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

            $info['token_type'] = config('api.token_type');
            $info['token_name'] = config('api.token_name');
            $info['token_exps'] = $info['token_exp'] * 3600;

            $cache->set($key, $info);
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
     * @param array $param 设置信息
     */
    public static function edit($param)
    {
        $model = self::model();
        $id    = self::$id;

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $info = $model->find($id);
        $res  = $info->save($param);
        if (empty($res)) {
            exception();
        }

        $cache = self::cache();
        $cache->clear();

        return $param;
    }
}
