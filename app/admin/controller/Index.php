<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\facade\View;

class Index
{
    public function index()
    {
        return View::fetch();
    }

    public function console()
    {
        return View::fetch();
    }
}
