<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\system\SettingCache as Cache;
use app\common\model\system\SettingModel as Model;
use app\common\utils\Utils;
use app\common\utils\CaptchaUtils;
use app\common\utils\AjCaptchaUtils;

/**
 * 系统设置
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
     * 系统设置id
     * @var integer
     */
    private static $id = 1;

    /**
     * 菜单类型：目录
     * @var integer
     */
    const MENU_TYPE_CATALOGUE = 0;
    /**
     * 菜单类型：菜单
     * @var integer
     */
    const MENU_TYPE_MENU      = 1;
    /**
     * 菜单类型：按钮
     * @var integer
     */
    const MENU_TYPE_BUTTON    = 2;
    /**
     * 菜单类型数组或名称
     * @param string $menu_type 菜单类型
     * @param bool   $val_lab   是否返回带value、label下标的数组
     * @return array|string 
     */
    public static function menuTypes($menu_type = '', $val_lab = false)
    {
        $menu_types = [
            self::MENU_TYPE_CATALOGUE => lang('目录'),
            self::MENU_TYPE_MENU      => lang('菜单'),
            self::MENU_TYPE_BUTTON    => lang('按钮'),
        ];
        if ($menu_type !== '') {
            return $menu_types[$menu_type] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($menu_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }

        return $menu_types;
    }

    /**
     * 日志类型：登录
     * @var integer
     */
    const LOG_TYPE_LOGIN     = 0;
    /**
     * 日志类型：操作
     * @var integer
     */
    const LOG_TYPE_OPERATION = 1;
    /**
     * 日志类型：退出
     * @var integer
     */
    const LOG_TYPE_LOGOUT    = 2;
    /**
     * 日志类型数组或名称
     * @param string $log_type 日志类型
     * @param bool   $val_lab  是否返回带value、label下标的数组
     * @return array|string
     */
    public static function logTypes($log_type = '', $val_lab = false)
    {
        $log_types = [
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
     * 公告类型：通知
     * @var integer
     */
    const NOTICE_TYPE_NOTIFY = 0;
    /**
     * 公告类型：公告
     * @var integer
     */
    const NOTICE_TYPE_NOTICE = 1;
    /**
     * 公告类型数组或名称
     * @param int  $notice_type 公告类型
     * @param bool $val_lab  是否返回带value、label下标的数组
     */
    public static function noticeTypes($notice_type = '', $val_lab = false)
    {
        $notice_types = [
            self::NOTICE_TYPE_NOTIFY => lang('通知'),
            self::NOTICE_TYPE_NOTICE => lang('公告'),
        ];
        if ($notice_type !== '') {
            return $notice_types[$notice_type] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($notice_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }

        return $notice_types;
    }

    /**
     * 性别：未知
     * @var integer
     */
    const GENDER_UNKNOWN = 0;
    /**
     * 性别：男
     * @var integer
     */
    const GENDER_MAN = 1;
    /**
     * 性别：女
     * @var integer
     */
    const GENDER_WOMAN = 2;
    /**
     * 性别数组或名称
     * @param int  $gender  性别
     * @param bool $val_lab 是否返回带value、label下标的数组
     */
    public static function genders($gender = '', $val_lab = false)
    {
        $gender_types = [
            self::GENDER_UNKNOWN => lang('未知'),
            self::GENDER_MAN     => lang('男'),
            self::GENDER_WOMAN   => lang('女'),
        ];
        if ($gender !== '') {
            return $gender_types[$gender] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($gender_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $gender_types;
    }

    /**
     * 请求方法
     * @return array
     */
    public static function methods()
    {
        return ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS'];
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
     * 系统设置信息
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @return array
     * @Apidoc\Returned(ref={Model::class}, withoutField="setting_id")
     * @Apidoc\Returned(ref={Model::class,"getFaviconUrlAttr"}, field="favicon_url")
     * @Apidoc\Returned(ref={Model::class,"getLogoUrlAttr"}, field="logo_url")
     * @Apidoc\Returned(ref={Model::class,"getLoginBgUrlAttr"}, field="login_bg_url")
     * @Apidoc\Returned("cache_type", type="string", require=false, default="", desc="缓存类型")
     * @Apidoc\Returned("token_type", type="string", require=false, default="", desc="token方式")
     * @Apidoc\Returned("token_name", type="string", require=false, default="", desc="token名称")
     */
    public static function info($fields = '')
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
                $info['token_key']   = uniqids();
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }

            // 命令行无法获取域名
            if ($type == 'cgi') {
                $append = ['favicon_url', 'logo_url', 'login_bg_url'];
                $hidden = ['favicon', 'logo', 'loginbg'];
                $info = $info->append($append)->hidden($hidden);
            }
            $info = $info->toArray();

            $info['cache_type'] = config('cache.default');
            $info['token_type'] = config('admin.token_type');
            $info['token_name'] = config('admin.token_name');
            $info['token_exps'] = $info['token_exp'] * 3600;

            $cache->set($key, $info);
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
     * 系统设置修改
     * @param array $param 设置信息
     * @Apidoc\Param(ref={Model::class}, withoutField="setting_id,create_uid,update_uid,create_time,update_time")
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

    /**
     * 系统缓存清除
     */
    public static function cacheClear()
    {
        $base  = '../app/common/cache/';
        $paths = [$base . '*.php', $base . '*/*.php', $base . '*/*/*.php'];
        $tags  = [];
        foreach ($paths as $path) {
            $classs = glob($path);
            foreach ($classs as $class) {
                $class = str_replace(['..', '/', '.php'], ['', '\\', ''], $class);
                $Cache = new $class;
                $tag   = '';
                if (property_exists($Cache, 'allowClear')) {
                    if ($Cache->allowClear) {
                        $tag = $Cache->tag;
                    }
                } else {
                    $tag = $Cache->tag;
                }
                if ($tag) {
                    $tags[] = $tag;
                }
            }
        }
        $cache = self::cache();
        $clear = $cache->cache()::tag($tags)->clear();

        return ['clear' => $clear];
    }

    /**
     * 系统服务器信息
     * @param bool $force 是否强制刷新
     * @Apidoc\Query("force", type="int", default=0, desc="是否强制刷新")
     */
    public static function serverInfo($force = false)
    {
        $cache = self::cache();
        $key = 'serverInfo';

        if ($force) {
            $cache->del($key);
        }

        $data = $cache->get($key);
        if (empty($data)) {
            $data = Utils::serverInfo();
            $cache->set($key, $data);
        }

        return $data;
    }
}
