<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\content;

use hg\apidoc\annotation as Apidoc;
use app\common\cache\content\SettingCache as Cache;
use app\common\model\content\SettingModel as Model;

/**
 * 内容设置
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
     * 内容设置id
     */
    protected static $id = 1;

    /**
     * 内容设置信息
     * @param string $fields 返回字段，逗号隔开，默认所有
     * @return array
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getContentDefaultImgUrlAttr"}, field="content_default_img_url")
     * @Apidoc\Returned(ref={Model::class,"getCategoryDefaultImgUrlAttr"}, field="category_default_img_url")
     * @Apidoc\Returned(ref={Model::class,"getTagDefaultImgUrlAttr"}, field="tag_default_img_url")
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
                $append = array_merge($append, ['content_default_img_url', 'category_default_img_url', 'tag_default_img_url']);
                $hidden = array_merge($hidden, ['contentDefaultImg', 'categoryDefaultImg', 'tagDefaultImg']);
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
     * 内容设置修改
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
