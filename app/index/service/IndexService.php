<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 首页
namespace app\index\service;

class IndexService
{
    /**
     * 首页
     *
     * @return array
     */
    public static function index()
    {
        $data['name']   = 'yylAdmin';
        $data['desc']   = '基于ThinkPHP6和Vue2的极简后台管理系统';
        $data['gitee']  = 'https://gitee.com/skyselang/yylAdmin';
        $data['github'] = 'https://github.com/skyselang/yylAdmin';

        return $data;
    }
}
