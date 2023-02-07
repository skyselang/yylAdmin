<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\content;

use app\common\cache\content\SettingCache;
use app\common\model\content\SettingModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 内容设置
 */
class SettingService
{
    /**
     * 设置id
     * @var integer
     */
    protected static $id = 1;

    /**
     * 设置信息
     * 
     * @Apidoc\Returned("favicon_url", type="string", desc="favicon链接")
     * @Apidoc\Returned("logo_url", type="string", desc="logo链接")
     * @Apidoc\Returned("offi_url", type="string", desc="二维码链接")
     * @Apidoc\Returned("mini_url", type="string", desc="小程序码链接")
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
                $info['diy_config']  = [];
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }
            $info = $info->append(['favicon_url', 'logo_url', 'offi_url', 'mini_url', 'diy_con_obj'])->toArray();

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 设置修改
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

        SettingCache::del($id);

        return $param;
    }
}
