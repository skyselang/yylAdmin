<?php

namespace app\index\controller;

class Index
{
    public function index()
    {
        $data['Github'] = 'https://github.com/skyselang/yyl-admin';
        $data['Gitee'] = 'https://gitee.com/skyselang/yyl-admin';
        $data['desc'] = 'yylAdmin基于ThinkPHP6和Element2的极简后台管理系统，前后端分离。 https://github.com/skyselang/yyl-admin';

        return success($data);
    }
}
