<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 地区管理模型
namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;

class RegionModel extends Model
{
    protected $name = 'region';

    /**
     * @Field("region_id")
     */
    public function id()
    {
    }

    /**
     * @Field("region_id,region_pid,region_path,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_sort")
     */
    public function list()
    {
    }

    /**
     * 
     */
    public function info()
    {
    }

    /**
     * @Field("region_pid,region_level,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_longitude,region_latitude,region_sort")
     */
    public function add()
    {
    }

    /**
     * @Field("region_id,region_pid,region_level,region_name,region_pinyin,region_jianpin,region_initials,region_citycode,region_zipcode,region_longitude,region_latitude,region_sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("region_id")
     */
    public function dele()
    {
    }
}
