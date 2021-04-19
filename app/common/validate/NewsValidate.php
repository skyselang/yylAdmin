<?php
/*
 * @Description  : 新闻管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-09
 * @LastEditTime : 2021-04-19
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\NewsService;

class NewsValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'news_id'    => ['require', 'checkId'],
        'img'        => ['require'],
        'title'      => ['require'],
        'time'       => ['require'],
        'source_url' => ['url'],
        'content'    => ['require'],
        'image'      => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '204800'],
        'file'       => ['require', 'file', 'fileSize' => '10485760'],
    ];

    // 错误信息
    protected $message = [
        'news_id.require' => '缺少参数：新闻ID',
        'img.require'     => '请上传图片',
        'title.require'   => '请输入标题',
        'time.require'    => '请输入时间',
        'source_url.url'  => '请输入有效的来源链接',   
        'content.require' => '请输入内容',
        'image.require'   => '请选择图片',
        'image.file'      => '请选择文件',
        'image.image'     => '请选择图片格式文件',
        'image.fileExt'   => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'  => '请选择小于200kb的图片',
        'file.require'    => '请选择文件',
        'file.file'       => '请选择上传文件',
        'file.fileSize'   => '请选择小于10M的文件',
    ];

    // 验证场景
    protected $scene = [
        'id'     => ['news_id'],
        'info'   => ['news_id'],
        'add'    => ['img', 'title', 'time', 'source_url', 'content'],
        'edit'   => ['news_id', 'img', 'title', 'time', 'source_url', 'content'],
        'dele'   => ['news_id'],
        'istop'  => ['news_id'],
        'ishot'  => ['news_id'],
        'isrec'  => ['news_id'],
        'ishide' => ['news_id'],
        'image'  => ['image'],
        'file'   => ['file'],
        'last'   => ['news_id'],
        'next'   => ['news_id'],
    ];

    // 自定义验证规则：新闻是否存在
    protected function checkId($value, $rule, $data = [])
    {
        $news_id = $value;

        $news = NewsService::info($news_id);

        if ($news['is_delete'] == 1) {
            return '新闻已被删除：' . $news_id;
        }

        return true;
    }
}
