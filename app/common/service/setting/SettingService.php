<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use app\common\cache\setting\SettingCache;
use app\common\model\setting\SettingModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 设置管理
 */
class SettingService
{
    /**
     * 设置id
     * @var integer
     */
    private static $id = 1;

    /**
     * 反馈类型：功能异常
     * @var integer
     */
    const FEEDBACK_TYPE_EXCEPTIONAL = 0;
    /**
     * 反馈类型：产品建议
     * @var integer
     */
    const FEEDBACK_TYPE_ADVISE = 1;
    /**
     * 反馈类型：其它
     * @var integer
     */
    const FEEDBACK_TYPE_OTHER = 2;
    /**
     * 反馈类型
     * @param string $feedback_type 反馈类型
     * @return array|string 反馈类型数组或名称
     */
    public static function feedback_types($feedback_type = '')
    {
        $feedback_types = [
            self::FEEDBACK_TYPE_EXCEPTIONAL => '功能异常',
            self::FEEDBACK_TYPE_ADVISE => '产品建议',
            self::FEEDBACK_TYPE_OTHER => '其它',
        ];
        if ($feedback_type !== '') {
            return $feedback_types[$feedback_type] ?? '';
        }
        return $feedback_types;
    }

    /**
     * 通告类型：通知
     * @var integer
     */
    const NOTICE_TYPE_NOTIFY = 0;
    /**
     * 通告类型：公告
     * @var integer
     */
    const NOTICE_TYPE_NOTICE = 1;
    /**
     * 通告类型
     * @param string $notice_type 通告类型
     * @return array|string 通告类型数组或名称
     */
    public static function notice_types($notice_type = '')
    {
        $notice_types = [
            self::NOTICE_TYPE_NOTIFY => '通知',
            self::NOTICE_TYPE_NOTICE => '公告',
        ];
        if ($notice_type !== '') {
            return $notice_types[$notice_type] ?? '';
        }
        return $notice_types;
    }

    /**
     * 设置信息
     * 
     * @param string $field 指定字段
     * @Apidoc\Returned("favicon_url", type="string", desc="favicon链接")
     * @Apidoc\Returned("logo_url", type="string", desc="logo链接")
     * @Apidoc\Returned("offi_url", type="string", desc="二维码链接")
     * @Apidoc\Returned("mini_url", type="string", desc="小程序码链接")
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
                $append = ['favicon_url', 'logo_url', 'offi_url', 'mini_url', 'diy_con_obj'];
            } else {
                if (strpos($field, 'favicon_id') !== false) {
                    $append[] = 'favicon_url';
                }
                if (strpos($field, 'logo_id') !== false) {
                    $append[] = 'logo_url';
                }
                if (strpos($field, 'offi_id') !== false) {
                    $append[] = 'offi_url';
                }
                if (strpos($field, 'mini_id') !== false) {
                    $append[] = 'mini_url';
                }
                if (strpos($field, 'diy_config') !== false) {
                    $append[] = 'diy_con_obj';
                }
            }
            $append[] = 'feedback_type';
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
