<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\content;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\content\SettingValidate as Validate;
use app\common\service\content\SettingService as Service;

/**
 * @Apidoc\Title("lang(内容设置)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("200")
 */
class Setting extends BaseController
{
    /**
     * 验证器
     */
    protected $validate = Validate::class;

    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * @Apidoc\Title("lang(基本设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="content_default_img_open,content_default_img_id,category_default_img_open,category_default_img_id,tag_default_img_open,tag_default_img_id,content_default_img_url,category_default_img_url,tag_default_img_url")
     */
    public function basicInfo()
    {
        $data = $this->service::info('content_default_img_open,content_default_img_id,category_default_img_open,category_default_img_id,tag_default_img_open,tag_default_img_id,content_default_img_url,category_default_img_url,tag_default_img_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(基本设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="content_default_img_open,content_default_img_id,category_default_img_open,category_default_img_id,tag_default_img_open,tag_default_img_id")
     */
    public function basicEdit()
    {
        $param = $this->params([
            'content_default_img_open/d'  => 0,
            'content_default_img_id/d'    => 0,
            'category_default_img_open/d' => 0,
            'category_default_img_id/d'   => 0,
            'tag_default_img_open/d'      => 0,
            'tag_default_img_id/d'        => 0,
        ]);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(前台设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="is_api_content")
     */
    public function apiInfo()
    {
        $data = $this->service::info('is_api_content');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(前台设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="is_api_content")
     */
    public function apiEdit()
    {
        $param = $this->params([
            'is_api_content/d' => 0,
        ]);

        validate($this->validate)->scene('edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }
}
