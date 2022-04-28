<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理
namespace app\common\service\setting;

use think\facade\Config;
use app\common\cache\setting\SettingCache;
use app\common\model\setting\SettingModel;

class SettingService
{
    // 设置id
    private static $id = 1;

    /**
     * 设置信息
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
                $info['token_name']  = Config::get('api.token_name');
                $info['token_key']   = uniqid();
                $info['diy_config']  = serialize([]);
                $info['create_time'] = datetime();
                $model->insert($info);
                $info = $model->find($id);
            }
            $info = $info->toArray();
            
            if ($info['diy_config']) {
                $info['diy_config'] = unserialize($info['diy_config']);
            } else {
                $info['diy_config'] = [];
            }

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
        $pk = $model->getPk();
        $id = self::$id;

        $param['update_time'] = datetime();
        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($id);

        return $param;
    }

    /**
     * 自定义设置
     *
     * @return bool|Exception
     */
    public static function diy()
    {
        $setting = self::info();

        $diy = [];
        foreach ($setting['diy_config'] as $v) {
            $diy[$v['config_key']] = $v['config_val'];
        }

        return $diy;
    }
}
