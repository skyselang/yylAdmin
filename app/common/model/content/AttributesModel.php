<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\content;

use think\model\Pivot;

/**
 * 内容属性关联模型
 */
class AttributesModel extends Pivot
{
    // 表名
    protected $name = 'content_attributes';
    // 表主键
    protected $pk = 'id';

    // 关联内容
    public function content()
    {
        return $this->hasMany(ContentModel::class, 'content_id', 'content_id')->where([where_delete()]);
    }

    // 关联分类
    public function category()
    {
        return $this->hasOne(CategoryModel::class, 'category_id', 'category_id')->where([where_delete()]);
    }
    // 获取分类名称
    public function getCategoryNameAttr()
    {
        return $this['category']['category_name'] ?? '';
    }
}
