<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Index extends Common
{
    public function index()
    {
    	
    	$this->assign('index','layui-this');
        return $this->fetch();
    }
}
