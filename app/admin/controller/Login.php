<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\facade\View;

class Login
{
    public function login()
    {
        return View::fetch();
    }
}
