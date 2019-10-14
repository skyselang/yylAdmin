<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\Request;
use think\facade\View;

class Login extends Base
{
    public function login()
    {
        echo 'login';
        return View::fetch();
    }
}
