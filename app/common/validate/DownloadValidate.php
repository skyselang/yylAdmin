<?php
/*
 * @Description  : 下载管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\DownloadService;

class DownloadValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'download'    => ['require', 'array'],
        'download_id' => ['require'],
        'name'        => ['require'],
        'content'     => ['require'],
        'image'       => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'video'       => ['require', 'file', 'fileExt' => 'mp4,avi,wmv,rm,ram,mov,swf,flv,mpg,mpeg', 'fileSize' => '52428800'],
        'file'        => ['require', 'file', 'fileSize' => '10485760'],
        'is_top'      => ['require', 'in' => '0,1'],
        'is_hot'      => ['require', 'in' => '0,1'],
        'is_rec'      => ['require', 'in' => '0,1'],
        'is_hide'     => ['require', 'in' => '0,1'],
        'sort_field'  => ['checkSort'],
        'sort_type'   => ['checkSort'],
    ];

    // 错误信息
    protected $message = [
        'download_id'     => 'download_id must',
        'name.require'    => '请输入名称',
        'content.require' => '请输入内容',
        'image.require'   => '请选择图片',
        'image.file'      => '请选择上传图片',
        'image.image'     => '请选择图片格式文件',
        'image.fileExt'   => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'  => '请选择小于500kb的图片',
        'video.require'   => '请选择视频',
        'video.file'      => '请选择上传视频',
        'video.fileExt'   => '请选择mp4,avi,wmv,rm,ram,mov,swf,flv,mpg,mpeg格式视频',
        'video.fileSize'  => '请选择小于50M的视频',
        'file.require'    => '请选择文件',
        'file.file'       => '请选择上传文件',
        'file.fileSize'   => '请选择小于10M的文件',
        'is_top.require'  => 'is_top must',
        'is_top.in'       => 'is_top 1是0否',
        'is_hot.require'  => 'is_hot must',
        'is_hot.in'       => 'is_hot 1是0否',
        'is_rec.require'  => 'is_rec must',
        'is_rec.in'       => 'is_rec 1是0否',
        'is_hide.require' => 'is_hide must',
        'is_hide.in'      => 'is_hide 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['download_id'],
        'add'    => ['name', 'content'],
        'edit'   => ['download_id', 'name', 'content'],
        'dele'   => ['download'],
        'istop'  => ['download', 'is_top'],
        'ishot'  => ['download', 'is_hot'],
        'isrec'  => ['download', 'is_rec'],
        'ishide' => ['download', 'is_hide'],
        'image'  => ['image'],
        'video'  => ['video'],
        'file'   => ['file'],
        'sort'   => ['sort_field', 'sort_type'],
    ];

    // 自定义验证规则：排序字段是否存在，排序类型是否为asc、desc
    protected function checkSort($value, $rule, $data = [])
    {
        $sort_field = $data['sort_field'];
        $sort_type  = $data['sort_type'];

        $field_exist = DownloadService::tableFieldExist($sort_field);
        if (!$field_exist) {
            return '排序字段sort_field：' . $sort_field . ' 不存在';
        }

        if (!empty($sort_type) && $sort_type != 'asc' && $sort_type != 'desc') {
            return '排序类型sort_type只能为asc升序或desc降序';
        }

        return true;
    }
}
