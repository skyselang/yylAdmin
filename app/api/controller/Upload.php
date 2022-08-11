<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\BaseController;
use app\common\validate\file\FileValidate;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("上传")
 * @Apidoc\Group("upload")
 * @Apidoc\Sort("380")
 */
class Upload extends BaseController
{
    /**
     * @Apidoc\Title("上传文件")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\addParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function file()
    {
        $param['file']      = $this->request->file('file');
        $param['group_id']  = $this->param('group_id/d', 0);
        $param['file_type'] = $this->param('file_type/s', 'image');
        $param['file_name'] = $this->param('file_name/s', '');
        $param['is_front']  = 1;

        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);

        return success($data, '上传成功');
    }
}
