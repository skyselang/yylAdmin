<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\setting;

use think\facade\Request;
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
    const FEEDBACK_TYPE_ADVISE      = 1;
    /**
     * 反馈类型：其它
     * @var integer
     */
    const FEEDBACK_TYPE_OTHER       = 2;
    /**
     * 反馈类型数组或名称
     * @param string $feedback_type 反馈类型
     * @return array|string
     */
    public static function feedbackTypes($feedback_type = '')
    {
        $feedback_types = [
            self::FEEDBACK_TYPE_EXCEPTIONAL => '功能异常',
            self::FEEDBACK_TYPE_ADVISE      => '产品建议',
            self::FEEDBACK_TYPE_OTHER       => '其它',
        ];
        if ($feedback_type !== '') {
            return $feedback_types[$feedback_type] ?? '';
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
     * @param string $feedback_status 反馈状态
     * @return array|string
     */
    public static function feedbackStatuss($feedback_status = '')
    {
        $feedback_statuss = [
            self::FEEDBACK_STATUS_NOREPLY => '未回复',
            self::FEEDBACK_STATUS_REPLIED => '已回复',
        ];
        if ($feedback_status !== '') {
            return $feedback_statuss[$feedback_status] ?? '';
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
     * @param string $notice_type 通告类型
     * @return array|string
     */
    public static function noticeTypes($notice_type = '')
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
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @Apidoc\Returned("favicon_url", type="string", desc="favicon链接")
     * @Apidoc\Returned("logo_url", type="string", desc="logo链接")
     * @Apidoc\Returned("offi_url", type="string", desc="公众号二维码链接")
     * @Apidoc\Returned("mini_url", type="string", desc="小程序码链接")
     * @Apidoc\Returned("feedback_type", type="array", desc="反馈类型")
     * @return array
     */
    public static function info($fields = '')
    {
        $id   = self::$id;
        $type = Request::isCli() ? 'cli' : 'cgi';
        $key  = $id . $type;

        $info = SettingCache::get($key);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['create_uid']  = user_id();
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }

            // 命令行无法获取域名
            $append = ['feedback_type'];
            $hidden = [];
            if ($type == 'cgi') {
                $append = array_merge($append, ['favicon_url', 'logo_url', 'offi_url', 'mini_url']);
                $hidden = array_merge($hidden, ['favicon', 'logo', 'offi', 'mini']);
            }
            $info = $info->append($append)->hidden($hidden)->toArray();

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
