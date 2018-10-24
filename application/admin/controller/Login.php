<?php

namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{
	public function login()
	{
		return $this->fetch();	
	}
}
