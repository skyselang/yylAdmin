<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Common extends Controller
{
    public function initialize()
    {	
    	// 是否登录
        $login = Session::has('admin_id');
        if (!$login) {
        	$this->redirect(url('admin/login/login'));
        }
    }
}
