<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\setting\SettingCache as Cache;
use app\common\model\setting\SettingModel as Model;

/**
 * 设置管理
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
     * 设置管理id
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
    const FEEDBACK_TYPE_ADVISE      = 1;
    /**
     * 反馈类型：其它
     * @var integer
     */
    const FEEDBACK_TYPE_OTHER       = 2;
    /**
     * 反馈类型数组或名称
     * @param integer $feedback_type
     * @param bool    $val_lab 是否返回带value、label下标的数组
     */
    public static function feedbackTypes($feedback_type = '', $val_lab = false)
    {
        $feedback_types = [
            self::FEEDBACK_TYPE_EXCEPTIONAL => '功能异常',
            self::FEEDBACK_TYPE_ADVISE      => '产品建议',
            self::FEEDBACK_TYPE_OTHER       => '其它',
        ];

        if ($feedback_type !== '') {
            return $feedback_types[$feedback_type] ?? '';
        }

        if ($val_lab) {
            $val_labs = [];
            foreach ($feedback_types as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }

        return $feedback_types;
    }

    /**
     * 反馈状态：未回复
     * @var integer
     */
    const FEEDBACK_STATUS_NOREPLY = 0;
    /**
     * 反馈状态：已回复
     * @var integer
     */
    const FEEDBACK_STATUS_REPLIED = 1;
    /**
     * 反馈状态数组或名称
     * @param integer $feedback_status
     * @param bool    $val_lab 是否返回带value、label下标的数组
     */
    public static function feedbackStatuss($feedback_status = '', $val_lab = false)
    {
        $feedback_statuss = [
            self::FEEDBACK_STATUS_NOREPLY => '未回复',
            self::FEEDBACK_STATUS_REPLIED => '已回复',
        ];
        if ($feedback_status !== '') {
            return $feedback_statuss[$feedback_status] ?? '';
        }
        if ($val_lab) {
            $val_labs = [];
            foreach ($feedback_statuss as $key => $label) {
                $val_labs[] = ['value' => $key, 'label' => $label];
            }
            return $val_labs;
        }
        return $feedback_statuss;
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
     * 通告类型数组或名称
     * @param integer $notice_type 通告类型
     * @param bool    $val_lab 是否返回带value、label下标的数组
     */
    public static function noticeTypes($notice_type = '', $val_lab = false)
    {
        $notice_types = [
            self::NOTICE_TYPE_NOTIFY => '通知',
            self::NOTICE_TYPE_NOTICE => '公告',
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
     * 设置管理信息
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @return array
     * @Apidoc\Returned(ref={Model::class}, withoutField="setting_id")
     * @Apidoc\Returned(ref={Model::class,"getFaviconUrlAttr"}, field="favicon_url")
     * @Apidoc\Returned(ref={Model::class,"getLogoUrlAttr"}, field="logo_url")
     * @Apidoc\Returned(ref={Model::class,"getOffiUrlAttr"}, field="offi_url")
     * @Apidoc\Returned(ref={Model::class,"getMiniUrlAttr"}, field="mini_url")
     * @Apidoc\Returned(ref={Model::class,"getDouyinUrlAttr"}, field="douyin_url")
     * @Apidoc\Returned(ref={Model::class,"getVideoUrlAttr"}, field="video_url")
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
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }

            // 命令行无法获取域名
            $append = $hidden = [];
            if ($type == 'cgi') {
                $append = array_merge($append, ['favicon_url', 'logo_url', 'offi_url', 'mini_url', 'video_url', 'douyin_url']);
                $hidden = array_merge($hidden, ['favicon', 'logo', 'offi', 'mini', 'video', 'douyin']);
            }
            $info = $info->append($append)->hidden($hidden)->toArray();

            $cache->set($key, $info);
        }

        if ($fields) {
            $data   = [];
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
     * 设置管理修改
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
}
