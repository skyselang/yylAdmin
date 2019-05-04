<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{	

    public function index()
    {
    	echo "string";
        $this->redirect('admin/login/login');
    }

}
