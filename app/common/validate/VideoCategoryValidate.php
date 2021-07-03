<?php
/*
 * @Description  : 视频分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-30
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\VideoService;
use app\common\service\VideoCategoryService;

class VideoCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'video_category'    => ['require', 'array'],
        'video_category_id' => ['require'],
        'category_name'     => ['require', 'checkCategoryName'],
        'image'             => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'           => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'video_category_id'     => '缺少参数：视频分类id',
        'category_name.require' => '请输入分类名称',
        'image.require'         => '请选择图片',
        'image.file'            => '请选择上传图片',
        'image.image'           => '请选择图片格式文件',
        'image.fileExt'         => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'        => '请选择小于500kb的图片',
        'is_hide.require'       => 'is_hide must',
        'is_hide.in'            => 'is_hide 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['video_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['video_category_id', 'category_name'],
        'dele'   => ['video_category'],
        'ishide' => ['video_category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类删除
    protected function scenedele()
    {
        return $this->only(['video_category'])
            ->append('video_category', ['checkVideoCategory', 'checkVideo']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = VideoCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在视频
    protected function checkVideo($value, $rule, $data = [])
    {
        $video_category_ids = array_column($value, 'video_category_id');

        $where[] = ['video_category_id', 'in', $video_category_ids];
        $where[] = ['is_delete', '=', 0];

        $video = VideoService::list($where);
        if ($video['list']) {
            return '分类下存在视频，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkVideoCategory($value, $rule, $data = [])
    {
        $video_category_ids = array_column($value, 'video_category_id');

        $video_category = VideoCategoryService::list('list');
        foreach ($video_category as $k => $v) {
            foreach ($video_category_ids as $ka => $va) {
                if ($v['video_category_pid'] == $va) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }
}
