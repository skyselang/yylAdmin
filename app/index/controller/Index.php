<?php
/*
 * @Description  : 首页
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-17
 */

namespace app\index\controller;

class Index
{
    public function index()
    {
        $data['desc']   = 'yylAdmin基于ThinkPHP6和Element2的极简后台管理系统';
        $data['Github'] = 'https://github.com/skyselang/yylAdmin';
        $data['Gitee']  = 'https://gitee.com/skyselang/yylAdmin';

        return success($data);
    }
}
