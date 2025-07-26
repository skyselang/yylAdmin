<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

use think\facade\Db;
use think\facade\Config;
use think\facade\Request;
use think\exception\HttpException;
use app\common\utils\ReturnCodeUtils;
use app\common\service\system\UserTokenService;
use app\common\service\member\TokenService as MemberTokenService;

// 公共函数文件

/**
 * 成功返回
 * @param array  $data 数据
 * @param string $msg  提示
 * @param int    $code 返回码
 * @return \think\response\Json
 */
function success($data = [], $msg = '', $code = ReturnCodeUtils::SUCCESS)
{
	if ($msg === '') {
		$msg = lang('操作成功');
	}
	$res['code'] = $code;
	$res['msg']  = $msg;
	$res['data'] = $data;

	return json($res);
}

/**
 * 错误返回
 * @param string $msg  提示
 * @param array  $data 数据
 * @param int    $code 返回码
 * @return \think\response\Json
 */
function error($msg = '', $data = [], $code = ReturnCodeUtils::ERROR)
{
	if ($msg === '') {
		$msg = lang('操作失败');
	}
	$res['code'] = $code;
	$res['msg']  = $msg;
	$res['data'] = $data;

	return json($res);
}

/**
 * 错误返回（调试使用）
 * @param array  $data 数据
 * @param string $msg  提示
 * @param int    $code 返回码
 * @return \think\response\Json
 */
function error_e($data = [], $msg = '', $code = ReturnCodeUtils::ERROR)
{
	if ($msg === '') {
		$msg = lang('操作失败');
	}
	$res['code'] = $code;
	$res['msg']  = $msg;
	$res['time'] = time();
	$res['data'] = $data;

	print_r(json_encode($res, JSON_UNESCAPED_UNICODE));

	exit;
}

/**
 * 错误返回（抛出异常）
 * @param string $msg  提示
 * @param int    $code 返回码
 * @return HttpException
 */
function exception($msg = '', $code = ReturnCodeUtils::ERROR)
{
	if ($msg === '') {
		$msg = lang('操作失败');
	}
	throw new HttpException(200, $msg, null, [], $code);
}

/**
 * 服务器地址：协议://域名
 * @return string
 */
function server_url()
{
	return Request::domain();
}

/**
 * 文件地址：协议://域名/文件路径
 * @param string $file_path 文件路径
 * @return string
 */
function file_url($file_path = '')
{
	if (empty($file_path)) {
		return '';
	}
	if (stripos($file_path, 'http') === 0) {
		return $file_path;
	}
	$server_url = server_url();
	if (stripos($file_path, '/') === 0) {
		$file_url = $server_url . $file_path;
	} else {
		$file_url = $server_url . '/' . $file_path;
	}

	return $file_url;
}

/**
 * 文件ids
 * @param array  $files 文件列表 eg: [['file_id'=>1]]
 * @param string $field 文件id字段
 * @return array eg：[1,2,3]
 */
function file_ids($files = [], $field = 'file_id')
{
	$file_ids = [];
	foreach ($files as $val) {
		if ($val[$field] ?? 0) {
			$file_ids[] = $val[$field];
		}
	}

	return $file_ids;
}

/**
 * http get 请求
 * @param string $url    请求地址
 * @param array  $header 请求头部
 * @return array
 */
function http_get($url, $header = [])
{
	$header = array_merge($header, ['Content-type:application/json', 'Accept:application/json']);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	$response = curl_exec($curl);
	curl_close($curl);
	$response = json_decode($response, true);

	return $response;
}

/**
 * http post 请求
 * @param string $url    请求地址
 * @param array  $param  请求参数
 * @param array  $header 请求头部
 * @return array
 */
function http_post($url, $param = [], $header = [])
{
	$param  = json_encode($param);
	$header = array_merge($header, ['Content-type:application/json;charset=utf-8', 'Accept:application/json']);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($curl);
	curl_close($curl);
	$response = json_decode($response, true);

	return $response;
}

/**
 * 获取当前日期时间
 * @param string $format 格式，默认Y-m-d H:i:s
 * @return string
 */
function datetime($format = 'Y-m-d H:i:s')
{
	return date($format);
}

/**
 * 获取数组中重复的值
 * @param array $array 数组
 * @return array
 */
function array_repeat($array)
{
	$value_counts  = array_count_values($array);
	$value_repeats = [];
	foreach ($value_counts as $value => $count) {
		if ($count > 1) {
			$value_repeats[] = $value;
		}
	}

	return $value_repeats;
}

/**
 * 列表转树形
 * @param array  $list   列表
 * @param string $idk    id字段
 * @param string $pidk   pid字段
 * @param int    $root   根节点id
 * @param string $childk 子节点字段
 * @return array
 */
function list_to_tree($list = [], $idk = 'id', $pidk = 'pid', $root = 0, $childk = 'children')
{
	$refer = [];
	foreach ($list as $k => $v) {
		$refer[$v[$idk]] = &$list[$k];
	}

	$tree = [];
	foreach ($list as $key => $val) {
		$parent_id = 0;
		if (isset($val[$pidk])) {
			$parent_id = $val[$pidk];
		}
		if ($root == $parent_id) {
			$tree[] = &$list[$key];
		} else {
			if (isset($refer[$parent_id])) {
				$parent = &$refer[$parent_id];
				$parent[$childk][] = &$list[$key];
			}
		}
	}

	return $tree;
}

/**
 * 列表转路径
 * @param array  $list      列表
 * @param string $idk       id字段
 * @param string $pidk      pid字段
 * @param string $namek     名称字段
 * @param string $connector 连接符
 * @param int    $root      根节点ID
 * @return array 路径
 */
function list_to_path($list = [], $idk = 'id', $pidk = 'pid', $namek = 'name', $connector = '>', $root = 0)
{
	// 按父ID分组
	$grouped = [];
	foreach ($list as $val) {
		$grouped[$val[$pidk]][] = $val;
	}

	// 递归构建路径，从根节点开始
	$buildPath = function ($parentId, $currentPath) use (&$buildPath, $grouped, &$result, $idk, $namek, $connector) {
		if (!isset($grouped[$parentId])) {
			return;
		}

		foreach ($grouped[$parentId] as $val) {
			$path = $currentPath ? $currentPath . $connector . $val[$namek] : $val[$namek];
			$result[$path] = $val;

			// 递归处理子节点
			$buildPath($val[$idk], $path);
		}
	};

	$result = [];
	$buildPath($root, '');

	return $result;
}


/**
 * 列表转树形（根节点未知）
 * @param array  $list   列表
 * @param string $idk    id字段
 * @param string $pidk   pid字段
 * @param string $childk 子节点字段
 * @return array
 */
function array_to_tree($list = [], $idk = 'id', $pidk = 'pid', $childk = 'children')
{
	$parent_ids = [];
	foreach ($list as $val) {
		$pids = children_parent_key($list, $val[$idk], $idk, $pidk);
		$parent_ids[] = end($pids);
	}

	$parent_ids = array_unique($parent_ids);
	foreach ($list as &$v) {
		foreach ($parent_ids as $vp) {
			if ($v[$idk] === $vp) {
				$v[$pidk] = 0;
			}
		}
	}

	return list_to_tree($list, $idk, $pidk, 0, $childk);
}

/**
 * 树形转列表
 * @param array  $tree   树形
 * @param string $childk 子节点字段
 * @return array
 */
function tree_to_list($tree = [], $childk = 'children')
{
	$list = [];
	foreach ($tree as $val) {
		if (isset($val[$childk])) {
			$children = $val[$childk];
			unset($val[$childk]);
			$list[] = $val;
			if (is_array($children)) {
				$list = array_merge($list, tree_to_list($children, $childk));
			}
		} else {
			$list[] = $val;
		}
	}

	return $list;
}

/**
 * 树形是否存在回环
 * @param array  $list 列表
 * @param int    $id   id
 * @param int    $pid  pid
 * @param string $idk  id字段
 * @param string $pidk pid字段
 * @return bool
 */
function tree_is_cycle($list, $id, $pid, $idk = 'id', $pidk = 'pid')
{
	// 替换pid
	foreach ($list as &$value) {
		if ($value[$idk] == $id) {
			$value[$pidk] = $pid;
			break;
		}
	}

	// 构建邻接表
	$tree = [];
	foreach ($list as $menu) {
		$menu_id  = $menu[$idk];
		$menu_pid = $menu[$pidk];
		if (!isset($tree[$menu_pid])) {
			$tree[$menu_pid] = [];
		}
		$tree[$menu_pid][] = $menu_id;
	}

	// 深度优先搜索（DFS）
	$visited = []; // 已访问的节点
	$path    = [];    // 当前路径上的节点

	// 定义递归函数
	$dfs = function ($node_id) use (&$tree, &$visited, &$path, &$dfs) {
		// 如果当前节点已经在路径中，说明存在回环
		if (in_array($node_id, $path)) {
			return true;
		}

		// 如果当前节点未被访问过，进行处理
		if (!isset($visited[$node_id])) {
			$visited[$node_id] = true;
			$path[] = $node_id;

			// 遍历子节点
			if (isset($tree[$node_id])) {
				foreach ($tree[$node_id] as $child_id) {
					if ($dfs($child_id)) {
						return true;
					}
				}
			}

			// 移除当前节点路径标记
			array_pop($path);
		}

		return false;
	};

	// 遍历所有根节点（$pid 为 0 或不存在的节点）
	foreach ($tree[0] ?? [] as $root_node_id) {
		if ($dfs($root_node_id)) {
			return true;
		}
	}

	return false;
}

/**
 * 获取父级的所有子级
 * @param array  $list 列表
 * @param int    $id   id
 * @param string $idk  id字段
 * @param string $pidk pid字段
 * @param string $colk 列字段
 * @return array
 */
function parent_children_key($list, $id, $idk = 'id', $pidk = 'pid', $colk = '')
{
	if (empty($colk)) {
		$colk = $idk;
	}

	$children = [];
	foreach ($list as $v) {
		if ($v[$pidk] == $id) {
			$children[] = $v[$colk];
			$children = array_merge($children, parent_children_key($list, $v[$idk], $idk, $pidk, $colk));
		}
	}

	return $children;
}

/**
 * 获取子级的所有父级
 * @param array  $list 列表
 * @param int    $id   id
 * @param string $idk  id字段
 * @param string $pidk pid字段
 * @param string $colk 列字段
 * @return array
 */
function children_parent_key($list, $id, $idk = 'id', $pidk = 'pid', $colk = '')
{
	if (empty($colk)) {
		$colk = $idk;
	}

	$parent = [];
	foreach ($list as $v) {
		if ($v[$idk] == $id) {
			$parent[] = $v[$colk];
			$parent = array_merge($parent, children_parent_key($list, $v[$pidk], $idk, $pidk, $colk));
		}
	}

	return $parent;
}

/**
 * 变量类型转换为数组类型
 * @param mixed $var 要转换的变量
 * @return array
 */
function var_to_array($var)
{
	if (empty($var)) {
		return [];
	}

	if (is_string($var) && str_contains($var, ',')) {
		return explode(',', $var);
	}

	return (array) $var;
}

/**
 * 变量是否存在（||）
 * @param array $alls 所有的参数变量
 * @param array $vars 需要检查的变量
 * @return bool true 有任何一个变量存在，false 都不存在
 */
function var_isset($alls, $vars = [])
{
	if (is_string($vars)) {
		$vars = explode(',', $vars);
	}

	foreach ($vars as $var) {
		if (isset($alls[$var])) {
			return true;
		}
	}

	return false;
}

/**
 * 用户token
 * @return string
 */
function user_token()
{
	$config     = Config::get('admin');
	$token_name = $config['token_name'];

	// 优先从配置的方式获取
	if ($config['token_type'] === 'header') {
		$user_token = Request::header($token_name, '');
	} else {
		$user_token = Request::param($token_name, '');
	}

	// 如果没有获取到，尝试从另一种方式获取
	if (empty($user_token)) {
		$user_token = Request::param($token_name, '') ?: Request::header($token_name, '');
	}

	return $user_token;
}

/**
 * 用户token验证
 * @param string $user_token 用户token
 * @return Exception
 */
function user_token_verify($user_token = '')
{
	if (empty($user_token)) {
		$user_token = user_token();
	}

	UserTokenService::verify($user_token);
}

/**
 * 用户id
 * @param bool $exce 未登录是否抛出异常
 * @return int
 */
function user_id($exce = false)
{
	$user_token = user_token();
	$user_id    = UserTokenService::userId($user_token, $exce);

	return $user_id;
}

/**
 * 系统超管用户id（所有权限）
 * @return array
 */
function user_super_ids()
{
	return Config::get('admin.super_ids', []);
}

/**
 * 用户是否系统超管
 * @param int $user_id 用户id
 * @return bool
 */
function user_is_super($user_id)
{
	if (empty($user_id)) {
		return false;
	}
	$user_super_ids = user_super_ids();
	if (empty($user_super_ids)) {
		return false;
	}
	if (in_array($user_id, $user_super_ids)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 系统超管用户记录隐藏条件
 * @param string $user_id_field 用户id字段
 * @return array [$user_id_field, 'not in', $user_super_ids]
 */
function user_hide_where($user_id_field = 'user_id')
{
	$user_where = [];
	$super_hide = Config::get('admin.super_hide', false);
	if ($super_hide) {
		$user_id = user_id();
		if (!user_is_super($user_id)) {
			$user_super_ids = user_super_ids();
			if ($user_super_ids) {
				$user_where = [$user_id_field, 'not in', $user_super_ids];
			}
		}
	}

	return $user_where;
}

/**
 * 系统超管用户上传文件大小是否不受限制
 * @return bool
 */
function user_upload_limit()
{
	if (user_is_super(user_id())) {
		return Config::get('admin.super_upload_limit', false);
	}

	return false;
}

/**
 * 系统超管用户接口请求速率是否不受限制
 * @return bool
 */
function user_api_rate()
{
	if (user_is_super(user_id())) {
		return Config::get('admin.super_api_rate', false);
	}

	return false;
}

/**
 * 会员token获取
 * @return string
 */
function member_token()
{
	$config     = Config::get('api');
	$token_name = $config['token_name'];

	// 优先从配置的方式获取
	if ($config['token_type'] === 'header') {
		$member_token = Request::header($token_name, '');
	} else {
		$member_token = Request::param($token_name, '');
	}

	// 如果没有获取到，尝试从另一种方式获取
	if (empty($member_token)) {
		$member_token = Request::param($token_name, '') ?: Request::header($token_name, '');
	}

	return $member_token;
}

/**
 * 会员token验证
 * @param string $member_token 会员token
 * @return Exception
 */
function member_token_verify($member_token = '')
{
	if (empty($member_token)) {
		$member_token = member_token();
	}

	MemberTokenService::verify($member_token);
}

/**
 * 会员id获取
 * @param bool $exce 未登录是否抛出异常
 * @return int
 */
function member_id($exce = false)
{
	$member_token = member_token();

	return MemberTokenService::memberId($member_token, $exce);
}

/**
 * 会员超级会员id（所有权限）
 * @return array
 */
function member_super_ids()
{
	return Config::get('api.super_ids', []);
}

/**
 * 会员是否超级会员
 * @param int $member_id 会员id
 * @return bool
 */
function member_is_super($member_id)
{
	if (empty($member_id)) {
		return false;
	}

	$member_super_ids = member_super_ids();
	if (empty($member_super_ids)) {
		return false;
	}
	if (in_array($member_id, $member_super_ids)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 模型查询条件
 * @param think\Model $model 模型
 * @param array       $where 查询条件，二维数组
 * @param array       $scope 范围条件，二维数组
 * @param string      $mode  匹配模式：and所有条件匹配、or任一条件匹配
 * @return think\Model
 */
function model_where($model, $where, $scope = [], $mode = '')
{
	$where_query = $where_scope = [];

	if ($scope) {
		foreach ($scope as $value) {
			if (is_array($value)) {
				$where_scope[] = $value;
			} else {
				$where_scope[] = $scope;
				break;
			}
		}
	}

	foreach ($where as $val) {
		if ($val[0] === 'is_delete') {
			$where_scope[] = $val;
		} else {
			$where_query[] = $val;
		}
	}

	if ($mode === '') {
		$mode = Request::param('search_mode/s', 'and');
	}

	if ($mode === 'or') {
		$where_or = [];
		foreach ($where_query as $val) {
			$where_or[] = $val;
		}
		$model = $model->whereOr($where_or)->where($where_scope);
	} else {
		$model = $model->where($where_query)->where($where_scope);
	}

	return $model;
}

/**
 * 模型关联字段
 * @param Relation $relation 模型关联结果
 * @param string   $field    需要获取的字段
 * @param bool     $string   是否返回字符串
 * @param bool     $delete   是否筛选已删除
 * @param bool     $disable  是否筛选已禁用
 * @return string|array
 */
function model_relation_fields($relation, $field, $string = false, $delete = true, $disable = true)
{
	$names = [];
	$array = $relation ?? [];

	if ($array) {
		$array = is_array($array) ? $array : $array->toArray();
		foreach ($array as $key => $val) {
			if ($delete && $val['is_delete']) {
				unset($array[$key]);
			}
			if ($disable && $val['is_disable']) {
				unset($array[$key]);
			}
		}
	}
	if (empty($field)) {
		return $array;
	}

	$names = array_column($array, $field);
	if ($string) {
		return implode(separator(), $names);
	}

	return $names;
}

/**
 * 模型关联修改
 * @param Model  $info     模型find查询
 * @param array  $old_ids  旧的关联id
 * @param array  $new_ids  新的关联id
 * @param string $relation 关联方法名
 * @param array  $pivot    额外属性
 * @return void
 */
function model_relation_update($info, $old_ids, $new_ids, $relation, $pivot = [])
{
	if ($new_ids) {
		$new_diff = array_diff_assoc($old_ids, $new_ids);
		$old_diff = array_diff_assoc($new_ids, $old_ids);
		if ($new_diff || $old_diff) {
			$info->$relation()->detach($old_ids);
			$info->$relation()->attach($new_ids, $pivot);
		}
	} else {
		$info->$relation()->detach($old_ids);
	}
}

/**
 * 查询条件是否禁用
 * @param array $where   其它条件，['field','exp','value']或[['field','exp','value']...]
 * @param int   $disable 0未禁用，1已禁用
 * @return array $where为空返回一维数组:[]，$where不为空返回二维数组[[]...]。
 */
function where_disable($where = [], $disable = 0)
{
	$where_disable = ['is_disable', '=', $disable];
	if ($where) {
		$where_other = [];
		foreach ($where as $value) {
			if (is_array($value)) {
				$where_other[] = $value;
			} else {
				$where_other[] = $where;
				break;
			}
		}
		$where_other[] = $where_disable;
		$where_disable = $where_other;
	}

	return $where_disable;
}

/**
 * 查询条件是否删除
 * @param array $where  其它条件，['field','exp','value']或[['field','exp','value']...]
 * @param int   $delete 0未删除，1已删除
 * @return array $where为空返回一维数组[]，$where不为空返回二维数组[[]...]。
 */
function where_delete($where = [], $delete = 0)
{
	$where_delete = ['is_delete', '=', $delete];
	if ($where) {
		$where_other = [];
		foreach ($where as $value) {
			if (is_array($value)) {
				$where_other[] = $value;
			} else {
				$where_other[] = $where;
				break;
			}
		}
		$where_other[] = $where_delete;
		$where_delete = $where_other;
	}

	return $where_delete;
}

/**
 * 查询条件是否禁用、删除
 * @param array $where   其它条件，['field','exp','value']或[['field','exp','value']...]
 * @param int   $disable 0未禁用，1已禁用
 * @param int   $delete  0未删除，1已删除
 * @return array [[]...]
 */
function where_disdel($where = [], $disable = 0, $delete = 0)
{
	$where_disdel = [['is_disable', '=', $disable], ['is_delete', '=', $delete]];
	if ($where) {
		$where_other = [];
		foreach ($where as $value) {
			if (is_array($value)) {
				$where_other[] = $value;
			} else {
				$where_other[] = $where;
				break;
			}
		}
		$where_other[] = $where_disdel[0];
		$where_other[] = $where_disdel[1];
		$where_disdel  = $where_other;
	}

	return $where_disdel;
}

/**
 * 查询表达式
 * @param string $exp  需要返回的表达式，eg：=,>
 * @param bool   $null 是否包含null
 * @return array [['exp'=>'=','name'=>'包含']...]
 */
function where_exps($exp = '', $null = false)
{
	$exps = [
		['exp' => 'like', 'name' => lang('包含')],
		['exp' => 'not like', 'name' => lang('不包含')],
		['exp' => '=', 'name' => lang('等于')],
		['exp' => '<>', 'name' => lang('不等于')],
		['exp' => '>', 'name' => lang('大于')],
		['exp' => '>=', 'name' => lang('大于等于')],
		['exp' => '<', 'name' => lang('小于')],
		['exp' => '<=', 'name' => lang('小于等于')],
		['exp' => 'between', 'name' => lang('在区间')],
		['exp' => 'not between', 'name' => lang('不在区间')],
		['exp' => 'in', 'name' => lang('在列表')],
		['exp' => 'not in', 'name' => lang('不在列表')],
	];

	$exps_null = [
		['exp' => 'null', 'name' => lang('是null')],
		['exp' => 'not null', 'name' => lang('不是null')],
		['exp' => 'empty', 'name' => lang('是空的')],
		['exp' => 'not empty', 'name' => lang('不是空的')],
	];

	if ($null) {
		$exps = array_merge($exps, $exps_null);
	}

	if ($exp) {
		$exp = explode(',', $exp);
		$tmp = [];
		foreach ($exps as $val) {
			foreach ($exp as $v) {
				if ($val['exp'] == $v) {
					$tmp[] = $val;
				}
			}
		}
		$exps = $tmp;
	}

	return $exps;
}

/**
 * 查询表达式名称
 * @param string $exp 表达式，eg：=,>
 * @return string
 */
function where_exp_name($exp)
{
	$exps = where_exps('', true);
	$name = '';

	foreach ($exps as $val) {
		if ($val['exp'] === $exp) {
			$name = $val['name'];
			break;
		}
	}

	return $name;
}

/**
 * 查询条件字段缓存键名
 * @param string $type  类型
 * @param array  $where 条件
 * @param array  $order 排序
 * @param string $field 字段
 * @param int    $page  页数
 * @param int    $limit 数量
 * @param array  $param 参数
 * @return string
 */
function where_cache_key($type, $where, $order, $field, $page, $limit, $param = [])
{
	$field  = explode(',', $field);
	$params = [$type, $where, $order, $field, $page, $limit, $param];
	$keys   = [];
	foreach ($params as $val) {
		if (is_array($val)) {
			$val = json_encode($val);
		}
		$keys[] = $val;
	}
	$key_str = implode('-', $keys);
	$key_md5 = $type . md5($key_str);

	return $key_md5;
}

/**
 * 软删除更新数据
 * @param array  $data  其它数据
 * @param string $field 排除字段
 * @return array
 */
function update_softdele($data = [], $field = '')
{
	$data['is_delete']   = 1;
	$data['delete_uid']  = user_id();
	$data['delete_time'] = datetime();

	if ($field) {
		$fields = explode(',', $field);
		foreach ($fields as $field) {
			unset($data[$field]);
		}
	}

	return $data;
}

/**
 * 批量修改编号
 * @param \think\Model $model   模型
 * @param array        $ids     id数组：[1,2,3]
 * @param string       $field   编号字段：unique
 * @param string       $value   编号值(前缀,起始数)：SN,001
 * @param Service      $service 服务
 * @return array 编号数组：[1 => 'SN001', 2 => 'SN002', 3 => 'SN003']
 */
function update_unique($model, $ids, $field, $value, $service)
{
	$table = $model->getTable();
	$pk    = $model->getPk();

	$uniques = create_unique($ids, $value);
	$where   = [[$pk, 'not in', $ids], [$field, 'in', $uniques], ['is_delete', '=', 0]];
	$select  = Db::table($table)->where($where)->column($field);
	if ($select) {
		exception(lang('编号已存在：') . implode(',', $select));
	}

	$update_sql = 'UPDATE `' . $table . '` SET `' . $field . '` = CASE `' . $pk . '`';
	foreach ($uniques as $id => $val) {
		$update_sql .= ' WHEN ' . $id . " THEN '" . $val . "'";
	}
	$update_sql .= ' END WHERE ' . $pk . ' IN (' . implode(',', $ids) . ')';

	Db::query($update_sql);

	$service::edit($ids);

	return $uniques;
}

/**
 * 批量修改排序
 * @param \think\Model  $model   模型
 * @param array         $ids     id数组：[1,2,3]
 * @param string        $field   排序字段：sort
 * @param string        $value   排序值(排序,步长)：250,1
 * @param Service       $service 服务
 * @return int|array 250 | [1 => 250, 2 => 251, 3 => 252]
 */
function update_sort($model, $ids, $field, $value, $service)
{
	$table = $model->getTable();
	$pk    = $model->getPk();

	$sorts = create_sort($ids, $value);
	$param = [];
	if (is_numeric($sorts)) {
		$param = [$field => $sorts];
	} else {
		$update_sql = 'UPDATE `' . $table . '` SET `' . $field . '` = CASE `' . $pk . '`';
		foreach ($sorts as $id => $val) {
			$update_sql .= ' WHEN ' . $id . " THEN '" . $val . "'";
		}
		$update_sql .= ' END WHERE ' . $pk . ' IN (' . implode(',', $ids) . ')';
		Db::query($update_sql);
	}

	$service::edit($ids, $param);

	return $sorts;
}

/**
 * 批量修改数据
 * @param \think\Model $model   模型
 * @param array        $fields  字段 [['index'=>0,'field'=>'id]...]
 * @param array        $updates 数据
 */
function batch_update($model, $fields, $updates)
{
	if (empty($updates)) {
		return;
	}

	$table = $model->getTable();
	$pk    = $model->getPk();

	foreach ($fields as $field) {
		if ($field['index'] > 0) {
			$update_sql = 'UPDATE `' . $table . '` SET `' . $field['field'] . '` = CASE `' . $pk . '`';
			$update_ids = [];
			foreach ($updates as $update) {
				$update_val = $update[$field['field']];
				if ($update_val) {
					$update_val = str_replace("'", "''", $update_val);
				}
				if ($update_val === null) {
					$update_sql .= ' WHEN ' . $update[$pk] . " THEN NULL";
				} else {
					$update_sql .= ' WHEN ' . $update[$pk] . " THEN '" . $update_val . "'";
				}
				$update_ids[] = $update[$pk];
			}
			if ($update_ids) {
				$update_sql .= ' END WHERE ' . $pk . ' IN (' . implode(',', $update_ids) . ')';
			}
			Db::query($update_sql);
		}
	}
}

/**
 * 批量新增数据
 * @param \think\Model $model   模型
 * @param array        $fields  字段 ['index','field']
 * @param array        $inserts 数据
 */
function batch_insert($model, $fields, $inserts)
{
	if (empty($inserts)) {
		return;
	}

	$table = $model->getTable();
	$insert_fields = [];
	$insert_fields_sql = '';
	foreach ($fields as $field) {
		if ($field['index'] > 0) {
			$insert_fields[] = $field['field'];
			$insert_fields_sql .= '`' . $field['field'] . '`,';
		}
	}

	$insert_sql = 'INSERT INTO ' . $table . ' (' . rtrim($insert_fields_sql, ',') . ') VALUES ';
	foreach ($inserts as $insert) {
		$insert_sql_tmp = [];
		foreach ($insert_fields as $insert_field) {
			$insert_val = $insert[$insert_field];
			if ($insert_val) {
				$insert_val = str_replace("'", "''", $insert_val);
			}
			if ($insert_val === null) {
				$insert_sql_tmp[] = 'NULL';
			} else {
				$insert_sql_tmp[] = "'" . $insert_val . "'";
			}
		}
		$insert_sql .= '(' . implode(',', $insert_sql_tmp) . '),';
	}
	$insert_sql = rtrim($insert_sql, ',');

	Db::query($insert_sql);
}

/**
 * 生成编号
 * @param array  $ids   id数组：[1,2,3]
 * @param string $value 编号值(前缀,起始数)：SN,001
 * @return array [1 => 'SN001', 2 => 'SN002', 3 => 'SN003']
 */
function create_unique($ids, $value)
{
	$unique_arr    = explode(',', str_replace('，', ',', $value));
	$unique_prefix = $unique_arr[0] ?? 'SN';
	$unique_start  = $unique_arr[1] ?? 1;
	if (is_numeric($unique_prefix)) {
		exception(lang('前缀不能为纯数字'));
	}
	if (!is_numeric($unique_start)) {
		exception(lang('起始数必须是数字'));
	}

	$unique_count     = count($ids) - 1 + $unique_start;
	$unique_start_len = strlen($unique_start);
	$unique_array     = [];
	$ids_index        = 0;
	for ($index = $unique_start; $index <= $unique_count; $index++) {
		$unique_array[$ids[$ids_index]] = $unique_prefix . str_pad($index, $unique_start_len, '0', STR_PAD_LEFT);
		$ids_index++;
	}

	return $unique_array;
}

/**
 * 生成排序
 * @param array  $ids   id数组：[1,2,3]
 * @param string $value 排序值(排序,步长)：250,1
 * @return int|array 250 | [1 => 250, 2 => 251, 3 => 252]
 */
function create_sort($ids, $value)
{
	$sort_arr  = explode(',', str_replace('，', ',', $value));
	$sort_val  = $sort_arr[0] ?? 0;
	$sort_step = $sort_arr[1] ?? 0;
	if (!is_numeric($sort_val) || !is_numeric($sort_step)) {
		exception(lang('排序或步长必须是数字'));
	}

	$sort_array = [];
	if (empty($sort_step)) {
		$sort_array = $sort_val;
	} else {
		foreach ($ids as $key => $id) {
			$sort_array[$id] = $sort_val + ($sort_step * $key);
		}
	}

	return $sort_array;
}

/**
 * 生成state
 * @param string $prefix       前缀
 * @param bool   $more_entropy 是否增加额外的熵
 * @return string
 */
function create_state($prefix = '', $more_entropy = true)
{
	return md5(uniqid($prefix, $more_entropy));
}

/**
 * 获取当前语言
 * @return string
 */
function lang_get()
{
	$config = Config::get('lang');
	$lang   = '';
	if (Request::get($config['detect_var'])) {
		// url中设置了语言变量
		$lang = Request::get($config['detect_var']);
	} elseif (Request::header($config['header_var'])) {
		// Header中设置了语言变量
		$lang = Request::header($config['header_var']);
	}

	return $lang;
}

/**
 * 生成随机字符串
 * @param integer $length 长度（不包含前缀和后缀）
 * @param string  $prefix 前缀
 * @param string  $suffix 后缀
 * @return string
 */
function random_string($length = 16, $prefix = '', $suffix = '')
{
	$bytes     = random_bytes((int) ceil($length / 2));
	$string    = bin2hex($bytes);
	$stringLen = strlen($string);
	$diffLen   = $length - $stringLen;

	if ($diffLen > 0) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLen = strlen($characters);
		for ($i = 0; $i < $diffLen; $i++) {
			$string .= $characters[random_int(0, $charactersLen - 1)];
		}
	}

	return $prefix . substr($string, 0, $length) . $suffix;
}

/**
 * 生成唯一ID
 * @param string  $prefix       前缀
 * @param boolean $more_entropy 是否增加额外的熵
 */
function uniqids($prefix = '', $more_entropy = true)
{
	$uniqid    = uniqid($prefix, $more_entropy);
	$character = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string    = $character[random_int(0, strlen($character) - 1)];
	$uniqid    = str_replace('.', $string, $uniqid);

	return $uniqid;
}

/**
 * 从请求参数中排除指定字段
 * @param array $param   原始请求参数数组
 * @param array $exclude 需要排除的字段数组，例如: ['name', 'array.phone', 'list*price']
 * @return array 处理后的参数数组
 */
function request_param_exclude($param, $exclude)
{
	if (empty($param) || empty($exclude)) {
		return $param;
	}

	$exclude = array_filter($exclude);
	foreach ($exclude as $val) {
		if (!empty($val)) {
			if (str_contains($val, '.')) {
				$val = array_filter(explode('.', $val));
				$len = count($val);
				if ($len === 1) {
					unset($param[$val[0]]);
				} else if ($len === 2) {
					unset($param[$val[0]][$val[1]]);
				} else if ($len === 3) {
					unset($param[$val[0]][$val[1]][$val[2]]);
				}
			} else if (str_contains($val, '*')) {
				$val = array_filter(explode('*', $val));
				$len = count($val);
				if ($len === 2) {
					foreach ($param[$val[0]] as $k => $v) {
						unset($param[$val[0]][$k][$val[1]]);
					}
				}
			} else {
				unset($param[$val]);
			}
		}
	}

	return $param;
}

/**
 * 移除数组中值为空的元素
 * @param array $array 数组
 * @param array $keys  需要移除的键
 */
function unsets(&$array, $keys)
{
	foreach ($keys as $key) {
		if (empty($array[$key])) {
			unset($array[$key]);
		}
	}
}

/**
 * 连接符
 * @return string
 */
function connector()
{
	return '>';
}

/**
 * 分隔符
 * @return string
 */
function separator()
{
	return ';';
}
