<?php
/*
 * @Description  : 下载分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-30
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\DownloadService;
use app\common\service\DownloadCategoryService;

class DownloadCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'download_category'    => ['require', 'array'],
        'download_category_id' => ['require'],
        'category_name'        => ['require', 'checkCategoryName'],
        'image'                => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'              => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'download_category_id'  => '缺少参数：下载分类id',
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
        'info'   => ['download_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['download_category_id', 'category_name'],
        'dele'   => ['download_category'],
        'ishide' => ['download_category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类删除
    protected function scenedele()
    {
        return $this->only(['download_category'])
            ->append('download_category', ['checkDownloadCategory', 'checkDownload']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = DownloadCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在下载
    protected function checkDownload($value, $rule, $data = [])
    {
        $download_category_ids = array_column($value, 'download_category_id');

        $where[] = ['download_category_id', 'in', $download_category_ids];
        $where[] = ['is_delete', '=', 0];

        $download = DownloadService::list($where);
        if ($download['list']) {
            return '分类下存在下载，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkDownloadCategory($value, $rule, $data = [])
    {
        $download_category_ids = array_column($value, 'download_category_id');

        $download_category = DownloadCategoryService::list('list');
        foreach ($download_category as $k => $v) {
            foreach ($download_category_ids as $ka => $va) {
                if ($v['download_category_pid'] == $va) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }
}
