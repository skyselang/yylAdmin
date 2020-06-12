<?php

namespace app\api\controller;

class Index
{
    public function index()
    {
        $data['desc']   = 'yylAdmin基于ThinkPHP6和Element2的极简后台管理系统';
        $data['Github'] = 'https://github.com/skyselang/yyl-admin';
        $data['Gitee']  = 'https://gitee.com/skyselang/yyl-admin';

        return success($data);
    }
}
