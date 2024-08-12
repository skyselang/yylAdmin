<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\file\FileValidate;
use app\common\service\file\FileService;
use app\common\service\file\GroupService;
use app\common\service\file\TagService;
use app\common\service\file\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("上传")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("380")
 */
class Upload extends BaseController
{
    /**
     * @Apidoc\Title("上传文件")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param("group_id", type="string", require=false, desc="分组id或标识")
     * @Apidoc\Param("tag_ids", type="string", require=false, desc="标签id或标识，多个逗号隔开")
     * @Apidoc\Param(ref="app\common\model\file\FileModel", field="file_type,file_name")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function file()
    {
        $setting = SettingService::info();
        if (!$setting['is_upload_api']) {
            return error('文件上传未开启，无法上传文件！');
        }

        $param = $this->params([
            'group_id'    => 0,
            'tag_ids'     => '',
            'file_type/s' => 'image',
            'file_name/s' => '',
        ]);
        $param['file']     = $this->request->file('file');
        $param['is_front'] = 1;

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

        return success($data, '上传成功');
    }
}
