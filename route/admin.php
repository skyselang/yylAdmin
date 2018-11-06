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

// 绑定admin子域名到admin模块
Route::domain('admin', 'admin');

// login
Route::get('login', 'admin/login/login');
Route::post('login', 'admin/login/check');
Route::get('loginout','admin/login/loginout');

// index
Route::get('index', 'admin/index/index');

// news
Route::get('news', 'admin/news/index');
