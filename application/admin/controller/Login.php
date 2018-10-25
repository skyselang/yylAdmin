<?php

namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{
	public function login()
	{
		return $this->fetch();	
	}

	/**
	 * 退出系统
	 * @return [type] [description]
	 */
	public function loginout()
	{
		session(null);
		$this->success('退出成功！',url('login/login'));
	}
}
