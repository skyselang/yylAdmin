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
     * @param string $field 指定字段
     * @Apidoc\Returned("diy_con_obj", type="object", desc="自定义设置对象")
     * 
     * @return array
     */
    public static function info($field = '*')
    {
        $id = self::$id;
        $key = md5($id . $field);

        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->field($field)->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['diy_config']  = [];
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }

            $append = [];
            if ($field == '*') {
                $append = ['diy_con_obj'];
            } else {
                if (strpos($field, 'diy_config') !== false) {
                    $append[] = 'diy_con_obj';
                }
            }
            $info = $info->append($append)->toArray();

            SettingCache::set($key, $info);
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

        SettingCache::clear();

        return $param;
    }
}
