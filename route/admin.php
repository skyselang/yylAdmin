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

// login
Route::get('admin/login', 'admin/login/login');
Route::post('admin/login', 'admin/login/check');
Route::get('admin/exit','admin/login/exit');

// index
Route::get('admin/', 'admin/index/index');
Route::get('admin/indexs', 'admin/index/indexs');

