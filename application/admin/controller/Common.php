<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Common extends Controller
{
    public function initialize()
    {	
    	$this->isLogin();
    }

    /**
     * 是否登录
     * @return boolean 
     */
    public function isLogin()
    {
    	$is_login = Session::has('admin_id');
    	if ($is_login) {
    		# code...
    	} else {
    		Session::clear();//清除session（当前作用域）
    		echo "<script>parent.location.href='".url('admin/login/login')."'</script>";//js父级页面跳转
    	}
    }
}
