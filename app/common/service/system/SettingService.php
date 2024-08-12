<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use think\facade\Config;
use think\facade\Request;
use app\common\cache\Cache;
use app\common\cache\system\SettingCache;
use app\common\model\system\SettingModel;
use app\common\service\utils\Utils;
use app\common\service\utils\EmailUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * 系统设置
 */
class SettingService
{
    /**
     * 设置id
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
     * @return array|string 
     */
    public static function menuTypes($menu_type = '')
    {
        $menu_types = [
            self::MENU_TYPE_CATALOGUE => '目录',
            self::MENU_TYPE_MENU      => '菜单',
            self::MENU_TYPE_BUTTON    => '按钮',
        ];
        if ($menu_type !== '') {
            return $menu_types[$menu_type] ?? '';
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
     * @return array|string
     */
    public static function logTypes($log_type = '')
    {
        $log_types = [
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
     * 设置信息
     * 
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @Apidoc\Returned("logo_url", type="string", require=false, default="", desc="logo链接")
     * @Apidoc\Returned("favicon_url", type="string", require=false, default="", desc="favicon链接")
     * @Apidoc\Returned("login_bg_url", type="string", require=false, default="", desc="登录背景图链接")
     * @Apidoc\Returned("cache_type", type="string", require=false, default="", desc="缓存类型")
     * @Apidoc\Returned("token_type", type="string", require=false, default="", desc="token方式")
     * @Apidoc\Returned("token_name", type="string", require=false, default="", desc="token名称")
     * @return array
     */
    public static function info($fields = '')
    {
        $id = self::$id;
        $type = Request::isCli() ? 'cli' : 'cgi';
        $key = $id . $type;

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
            if ($type == 'cgi') {
                $append = ['favicon_url', 'logo_url', 'login_bg_url'];
                $hidden = ['favicon', 'logo', 'loginbg'];
                $info = $info->append($append)->hidden($hidden);
            }
            $info = $info->toArray();

            $cache_config = Cache::getConfig();
            $info['cache_type'] = $cache_config['default'];
            $info['token_type'] = Config::get('admin.token_type');
            $info['token_name'] = Config::get('admin.token_name');
            $info['token_exps'] = $info['token_exp'] * 3600;

            SettingCache::set($key, $info);
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
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @return bool|Exception
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
     * 缓存清除
     * 清除所有缓存标签数据
     *
     * @return array
     */
    public static function cacheClear()
    {
        $tags = [];
        $base = '../app/common/cache/';
        $paths = [$base . '*.php', $base . '*/*.php', $base . '*/*/*.php'];
        foreach ($paths as $path) {
            $caches = glob($path);
            foreach ($caches as $cache) {
                $cache = str_replace(['..', '/', '.php'], ['', '\\', ''], $cache);
                $Cache = new $cache;
                $tags[] = $Cache::$tag;
            }
        }

        sort($tags);
        $clear = Cache::tag($tags)->clear();

        return ['clear' => $clear, 'tags' => $tags];
    }

    /**
     * 邮箱测试
     *
     * @param array $param
     *
     * @return void
     */
    public static function emailTest($param)
    {
        $address = $param['email_test'];
        $subject = '测试邮件';
        $body    = '这是一封测试邮件，收到此邮件说明邮件设置和发送正常。';

        EmailUtils::send($address, $subject, $body);
    }

    /**
     * 服务器信息
     * 
     * @param bool $force 是否强制刷新
     *
     * @return array
     */
    public static function serverInfo($force = false)
    {
        $cache_key = 'utils:serverInfo';

        if ($force) {
            Cache::del($cache_key);
        }

        $data = Cache::get($cache_key);
        if (empty($data)) {
            $data = Utils::serverInfo();
            Cache::set($cache_key, $data, 86400);
        }

        return $data;
    }
}
