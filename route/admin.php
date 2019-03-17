<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::group('admin', function(){
	// login
	Route::get('login', 'admin/login/login');
	Route::post('login', 'admin/login/check');
	Route::get('exit','admin/login/exit');
	// index
	Route::get('/', 'admin/index/index');
	Route::get('indexs', 'admin/index/indexs');
});




