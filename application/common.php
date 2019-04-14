<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 公共函数文件
// 应用公共文件

use \Firebase\JWT\JWT;

define('JWTKEY', '1gHuiop975cdashyex9Ud23ldsvm2Xq'); //jwt密钥

function get_jwt_token($user_id = '', $username = '')
{
	$res['code'] = 401;

	$payload = [
	    'iss' => '', //签发者
	    'aud' => '', //jwt所面向的用户
	    'iat' => time(), //签发时间
	    'nbf' => time() + 0, //生效时间
	    'exp' => time() + 600, //过期时间
	    'data' => [
	        'user_id' => $user_id,
	        'username' => $username
	    ]
	];
	$token = JWT::encode($payload, JWTKEY);//生成token
	
}
exit();
$action = isset($_GET['action']) ? $_GET['action'] : '';

	 if ($action == 'login') {
	     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	         $username = htmlentities($_POST['user']);
	         $password = htmlentities($_POST['pass']);

	         if ($username == 'demo' && $password == 'demo') { //用户名和密码正确，则签发tokon
	             $nowtime = time();
	             $token = [
	                 'iss' => 'http://www.helloweba.net', //签发者
	                 'aud' => 'http://www.helloweba.net', //jwt所面向的用户
	                 'iat' => $nowtime, //签发时间
	                 'nbf' => $nowtime + 10, //在什么时间之后该jwt才可用
	                 'exp' => $nowtime + 600, //过期时间-10min
	                 'data' => [
	                     'userid' => 1,
	                     'username' => $username
	                 ]
	             ];
	             $jwt = JWT::encode($token, KEY);
	             $res['result'] = 'success';
	             $res['jwt'] = $jwt;
	         } else {
	             $res['msg'] = '用户名或密码错误!';
	         }
	     }
	     echo json_encode($res);

	 } else {
	     $jwt = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : '';
	     if (empty($jwt)) {
	         $res['msg'] = 'You do not have permission to access.';
	         echo json_encode($res);
	         exit;
	     }

	     try {
	         JWT::$leeway = 60;
	         $decoded = JWT::decode($jwt, KEY, ['HS256']);
	         $arr = (array)$decoded;
	         if ($arr['exp'] < time()) {
	             $res['msg'] = '请重新登录';
	         } else {
	             $res['result'] = 'success';
	             $res['info'] = $arr;
	         }
	     } catch(Exception $e) {
	         $res['msg'] = 'Token验证失败,请重新登录';
	     }

	     echo json_encode($res);
	 }