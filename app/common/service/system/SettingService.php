<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use think\facade\Cache;
use think\facade\Config;
use app\common\cache\system\UserCache;
use app\common\cache\system\SettingCache;
use app\common\model\system\UserModel;
use app\common\model\system\SettingModel;
use app\common\service\system\UserService;
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
    const MENU_TYPE_MENU = 1;
    /**
     * 菜单类型：按钮
     * @var integer
     */
    const MENU_TYPE_BUTTON = 2;
    /**
     * 菜单类型
     * @param string $menu_type 菜单类型
     * @return array|string 菜单类型数组或名称
     */
    public static function menu_types($menu_type = '')
    {
        $menu_types = [
            self::MENU_TYPE_CATALOGUE => '目录',
            self::MENU_TYPE_MENU => '菜单',
            self::MENU_TYPE_BUTTON => '按钮',
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
    const LOG_TYPE_LOGIN = 0;
    /**
     * 日志类型：操作
     * @var integer
     */
    const LOG_TYPE_OPERATION = 1;
    /**
     * 日志类型：退出
     * @var integer
     */
    const LOG_TYPE_LOGOUT = 2;
    /**
     * 日志类型
     * @param string $log_type 日志类型
     * @return array|string 日志类型数组或名称
     */
    public static function log_types($log_type = '')
    {
        $log_types = [
            self::LOG_TYPE_LOGIN => '登录',
            self::LOG_TYPE_OPERATION => '操作',
            self::LOG_TYPE_LOGOUT => '退出',
        ];
        if ($log_type !== '') {
            return $log_types[$log_type] ?? '';
        }
        return $log_types;
    }

    /**
     * 设置信息
     * 
     * @Apidoc\Returned("logo_url", type="string", require=false, default="", desc="logo链接")
     * @Apidoc\Returned("favicon_url", type="string", require=false, default="", desc="favicon链接")
     * @Apidoc\Returned("login_bg_url", type="string", require=false, default="", desc="登录背景图链接")
     * @Apidoc\Returned("token_type", type="string", require=false, default="", desc="token方式")
     * @Apidoc\Returned("token_name", type="string", require=false, default="", desc="token名称")
     *
     * @return array
     */
    public static function info()
    {
        $id = self::$id;
        
        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['token_key']   = uniqid();
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }
            $info = $info->append(['favicon_url', 'logo_url', 'login_bg_url'])->toArray();

            $cache_config = Cache::getConfig();
            $info['cache_type'] = $cache_config['default'];
            $info['token_type'] = Config::get('admin.token_type');
            $info['token_name'] = Config::get('admin.token_name');

            SettingCache::set($id, $info);
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

        SettingCache::del($id);

        return $param;
    }

    /**
     * 缓存清除
     *
     * @return array
     */
    public static function cacheClear()
    {
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $system = self::info();
        $token_anme = $system['token_name'];
        $user_cache = [];
        $user = $UserModel->field($UserPk)->where([where_delete()])->select()->toArray();
        foreach ($user as $v) {
            $user_old = UserCache::get($v[$UserPk]);
            if ($user_old) {
                $user_cache[] = [
                    $UserPk => $user_old[$UserPk],
                    $token_anme => $user_old[$token_anme],
                ];
            }
        }

        $res = Cache::clear();
        if (empty($res)) {
            exception();
        }

        foreach ($user_cache as $v) {
            $user_new = UserService::info($v[$UserPk], false);
            if ($user_new) {
                $user_new[$token_anme] = $v[$token_anme];
                UserCache::set($user_new[$UserPk], $user_new);
            }
        }

        $data['clear'] = $res;

        return $data;
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
}
