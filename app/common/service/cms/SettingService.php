<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容设置
namespace app\common\service\cms;

use app\common\cache\cms\SettingCache;
use app\common\model\cms\SettingModel;
use app\common\service\file\FileService;

class SettingService
{
    // 内容设置id
    protected static $id = 1;

    /**
     * 内容设置信息
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

            $info['logo_url']    = FileService::fileUrl($info['logo_id']);
            $info['off_acc_url'] = FileService::fileUrl($info['off_acc_id']);

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 内容设置修改
     *
     * @param array $param 内容信息
     *
     * @return array
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
}
