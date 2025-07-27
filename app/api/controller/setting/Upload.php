<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\file\FileValidate;
use app\common\service\file\FileService;
use app\common\service\file\GroupService;
use app\common\service\file\TagService;
use app\common\service\file\SettingService;

/**
 * @Apidoc\Title("lang(上传)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("200")
 */
class Upload extends BaseController
{
    /**
     * @Apidoc\Title("lang(上传文件)")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("group_id", type="string", require=false, desc="分组id或编号")
     * @Apidoc\Param("tag_ids", type="string", require=false, desc="标签id或编号，多个逗号隔开")
     * @Apidoc\Param(ref={FileService::class,"edit"}, field="file_type,file_name")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function file()
    {
        return $this->upload();
    }

    /**
     * @Apidoc\Title("lang(上传文件(登录后))")
     * @Apidoc\Desc("登录后上传文件调用此接口")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("group_id", type="string", require=false, desc="分组id或编号")
     * @Apidoc\Param("tag_ids", type="string", require=false, desc="标签id或编号，多个逗号隔开")
     * @Apidoc\Param(ref={FileService::class,"edit"}, field="file_type,file_name")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function files()
    {
        return $this->upload(true);
    }

    /**
     * @param bool $login 是否需要登录
     * @return array|string
     */
    private function upload($login = false)
    {
        $setting = SettingService::info();
        if (!$setting['is_upload_api']) {
            return error(lang('文件上传功能未开启'));
        }

        $param = $this->params([
            'group_id'    => 0,
            'tag_ids'     => '',
            'file_type/s' => 'image',
            'file_name/s' => '',
        ]);
        $param['file']      = $this->request->file('file');
        $param['member_id'] = member_id($login);
        $param['is_front']  = 1;

        validate(FileValidate::class)->scene('add')->check($param);

        if ($param['group_id']) {
            $group = GroupService::info($param['group_id'], false);
            $param['group_id'] = $group['group_id'] ?? 0;
        }
        $tag_ids = [];
        if ($param['tag_ids']) {
            if (is_string($param['tag_ids'])) {
                $param['tag_ids'] = explode(',', $param['tag_ids']);
            }
            foreach ($param['tag_ids'] as $tag_id) {
                $tag = tagService::info($tag_id, false);
                if ($tag) {
                    $tag_ids[] = $tag['tag_id'] ?? 0;
                }
            }
        }
        $param['tag_ids'] = $tag_ids;

        $data = FileService::add($param);

        return success($data, lang('上传成功'));
    }
}
