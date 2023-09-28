<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\content;

use think\facade\Request;
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
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @Apidoc\Returned("favicon_url", type="string", desc="favicon链接")
     * @Apidoc\Returned("logo_url", type="string", desc="logo链接")
     * @Apidoc\Returned("offi_url", type="string", desc="公众号二维码链接")
     * @Apidoc\Returned("mini_url", type="string", desc="小程序码链接")
     * @Apidoc\Returned("content_default_img_url", type="string", desc="内容默认图片链接")
     * @Apidoc\Returned("category_default_img_url", type="string", desc="分类默认图片链接")
     * @Apidoc\Returned("tag_default_img_url", type="string", desc="标签默认图片链接")
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
            $append = [];
            $hidden = [];
            if ($type == 'cgi') {
                $append = array_merge($append, ['favicon_url', 'logo_url', 'offi_url', 'mini_url', 'content_default_img_url', 'category_default_img_url', 'tag_default_img_url']);
                $hidden = array_merge($hidden, ['favicon', 'logo', 'offi', 'mini', 'contentDefaultImg', 'categoryDefaultImg', 'tagDefaultImg']);
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
