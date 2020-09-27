<?php
/*
 * @Description  : 首页
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-17
 * @LastEditTime : 2020-09-27
 */

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
        $data['desc']   = '基于ThinkPHP6和Element2的极简后台管理系统';
        $data['Github'] = 'https://github.com/skyselang/yylAdmin';
        $data['Gitee']  = 'https://gitee.com/skyselang/yylAdmin';

        return success($data);
    }
}
