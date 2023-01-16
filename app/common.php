<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 公共函数文件
use think\facade\Request;
use think\facade\Config;

/**
 * 成功返回
 *
 * @param array  $data 数据
 * @param string $msg  提示
 * @param int    $code 返回码
 * 
 * @return json
 */
function success($data = [], $msg = '操作成功', $code = 200)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['data'] = $data;

    return json($res);
}

/**
 * 错误返回
 *
 * @param array  $data 数据
 * @param string $msg  提示
 * @param int    $code 返回码
 * 
 * @return json
 */
function error($data = [], $msg = '操作失败',  $code = 400)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['data'] = $data;

    return json($res);
}

/**
 * 错误返回（调试时用）
 *
 * @param array  $data 数据
 * @param string $msg  提示
 * @param int    $code 返回码
 * 
 * @return json
 */
function error_e($data = [], $msg = '操作失败',  $code = 400)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['data'] = $data;

    print_r(json_encode($res, JSON_UNESCAPED_UNICODE));

    exit;
}

/**
 * 错误返回（抛出异常）
 *
 * @param string $msg  提示
 * @param int    $code 返回码
 * 
 * @return Exception
 */
function exception($msg = '操作失败', $code = 400)
{
    throw new \think\Exception($msg, $code);
}

/**
 * 服务器地址
 * 协议和域名
 *
 * @return string
 */
function server_url()
{
    return Request::domain();
}

/**
 * 文件地址
 * 协议/域名/文件路径
 *
 * @param string $file_path 文件路径
 * 
 * @return string
 */
function file_url($file_path = '')
{
    if (empty($file_path)) {
        return '';
    }

    if (strpos($file_path, 'http') !== false) {
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
 *
 * @param array  $files 文件列表 eg: [['file_id'=>1]]
 * @param string $field 文件id字段
 * 
 * @return array eg：[1,2,3]
 */
function file_ids($files = [], $field = 'file_id')
{
    foreach ($files as $val) {
        if ($val[$field] ?? 0) {
            $file_ids[] = $val[$field];
        }
    }
    return $file_ids ?? [];
}

/**
 * http get 请求
 *
 * @param string $url    请求地址
 * @param array  $header 请求头部
 *
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
 *
 * @param string $url    请求地址
 * @param array  $param  请求参数
 * @param array  $header 请求头部
 *
 * @return array
 */
function http_post($url, $param = [], $header = [])
{
    $param = json_encode($param);

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
 * 
 * @param string $format 格式，默认Y-m-d H:i:s
 *
 * @return string
 */
function datetime($format = 'Y-m-d H:i:s')
{
    return date($format);
}

/**
 * 列表转树形
 *
 * @param array   $list  列表数组
 * @param string  $pk    主键名称
 * @param string  $pid   父键名称
 * @param int     $root  根节点id
 * @param string  $child 子节点名称
 *
 * @return array
 */
function list_to_tree($list = [], $pk = 'id', $pid = 'pid', $root = 0,  $child = 'children')
{
    $tree  = [];
    $refer = [];
    foreach ($list as $k => $v) {
        $refer[$v[$pk]] = &$list[$k];
    }
    foreach ($list as $key => $val) {
        $parent_id = 0;
        if (isset($val[$pid])) {
            $parent_id = $val[$pid];
        }
        if ($root == $parent_id) {
            $tree[] = &$list[$key];
        } else {
            if (isset($refer[$parent_id])) {
                $parent = &$refer[$parent_id];
                $parent[$child][] = &$list[$key];
            }
        }
    }
    return $tree;
}

/**
 * 列表转树形（根节点未知）
 *
 * @param array   $list  列表数组
 * @param string  $pk    主键名称
 * @param string  $pid   父键名称
 * @param int     $root  根节点id
 * @param string  $child 子节点名称
 *
 * @return array
 */
function array_to_tree($list = [], $pk = 'id', $pid = 'pid',  $child = 'children')
{
    $parent_ids = [];
    foreach ($list as $val) {
        $pids = children_parentid($list, $val[$pk], $pk, $pid);
        $parent_ids[] = end($pids);
    }
    $parent_ids = array_unique($parent_ids);
    foreach ($list as &$v) {
        foreach ($parent_ids as $vp) {
            if ($v[$pk] == $vp) {
                $v[$pid] = 0;
            }
        }
    }
    return list_to_tree($list, $pk, $pid, $child);
}

/**
 * 树形转列表
 *
 * @param array  $tree  树形数组
 * @param string $child 子节点名称
 *
 * @return array
 */
function tree_to_list($tree = [], $child = 'children')
{
    $list = [];
    foreach ($tree as $val) {
        if (isset($val[$child])) {
            $children = $val[$child];
            unset($val[$child]);
            $list[] = $val;
            if (is_array($children)) {
                $list = array_merge($list, tree_to_list($children, $child));
            }
        } else {
            $list[] = $val;
        }
    }
    return $list;
}

/**
 * 变量类型转换为数组类型
 *
 * @param mixed $var 要转换的变量
 *
 * @return array
 */
function var_to_array($var)
{
    if (empty($var)) {
        return [];
    }

    if (is_string($var)) {
        if (strpos(',', $var) !== false) {
            $var = explode(',', $var);
        }
    }

    settype($var, 'array');

    return $var;
}

/**
 * 会员超级会员id（所有权限）
 *
 * @return array
 */
function member_super_ids()
{
    return Config::get('api.super_ids', []);
}

/**
 * 会员是否超级会员
 *
 * @param int $member_id 会员id
 * 
 * @return bool
 */
function member_is_super($member_id = 0)
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
 * 获取父级的所有子级id
 *
 * @param array  $list 列表
 * @param int    $id   id
 * @param string $pk   主键
 * @param string $pid  父键
 *
 * @return array id数组
 */
function parent_children($list, $id, $pk = 'id', $pid = 'pid')
{
    $children = [];
    foreach ($list as $v) {
        if ($v[$pid] == $id) {
            $children[] = $v[$pk];
            $children = array_merge($children, parent_children($list, $v[$pk], $pk, $pid));
        }
    }
    return $children;
}

/**
 * 获取子级的所有父级id
 *
 * @param array  $list 列表
 * @param int    $id   id
 * @param string $pk   主键
 * @param string $pid  父键
 *
 * @return array id数组
 */
function children_parentid($list, $id, $pk = 'id', $pid = 'pid')
{
    $parentid = [];
    foreach ($list as $v) {
        if ($v[$pk] == $id) {
            $parentid[] = $v[$pk];
            $parentid = array_merge($parentid, children_parentid($list, $v[$pid], $pk, $pid));
        }
    }
    return $parentid;
}

/**
 * 获取模型关联字段
 *
 * @param Relation $relation 模型关联结果
 * @param string   $field    需要获取的字段
 * @param bool     $string   是否返回字符串
 * @param bool     $delete   是否筛选已删除
 *
 * @return string|array
 */
function relation_fields($relation, $field, $string = false, $delete = true)
{
    $names = [];
    $array = $relation ?? [];
    if ($array) {
        $array = $array->toArray();
        if ($delete) {
            foreach ($array as $val) {
                if (!$val['is_delete']) {
                    $names[] = $val[$field];
                }
            }
        } else {
            $names = array_column($array, $field);
        }
    }
    if ($string) {
        return implode(',', $names);
    }
    return $names;
}

/**
 * 查询条件是否禁用
 *
 * @param array $where   其它条件
 * @param int   $disable 0未禁用，1已禁用
 *
 * @return array
 */
function where_disable($where = [], $disable = 0)
{
    $where_other = [];
    $where_disable = ['is_disable', '=', $disable];
    if ($where) {
        foreach ($where as $value) {
            if (is_array($value)) {
                $where_other[] = $value;
            } else {
                $where_other[] = $where;
                break;
            }
        }
        $where_other[] = $where_disable;
        return $where_other;
    }
    return $where_disable;
}

/**
 * 查询条件是否删除
 *
 * @param array $where  其它条件
 * @param int   $delete 0未删除，1已删除
 *
 * @return array
 */
function where_delete($where = [], $delete = 0)
{
    $where_other = [];
    $where_delete = ['is_delete', '=', $delete];
    if ($where) {
        foreach ($where as $value) {
            if (is_array($value)) {
                $where_other[] = $value;
            } else {
                $where_other[] = $where;
                break;
            }
        }
        $where_other[] = $where_delete;
        return $where_other;
    }
    return $where_delete;
}

/**
 * 查询条件是否禁用、删除
 *
 * @param array $where   其它条件
 * @param int   $disable 0未禁用，1已禁用
 * @param int   $delete  0未删除，1已删除
 *
 * @return array
 */
function where_disdel($where = [], $disable = 0, $delete = 0)
{
    $where_other = [];
    $where_disdel = [['is_disable', '=', $disable], ['is_delete', '=', $delete]];
    if ($where) {
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
        return $where_other;
    }
    return $where_disdel;
}

/**
 * 查询表达式
 *
 * @param string $exp 需要返回的表达式，eg：=,>
 *
 * @return array [['exp'=>'=','name'=>'包含']...]
 */
function where_exps($exp = '')
{
    $exps = [
        ['exp' => 'like', 'name' => '包含'],
        ['exp' => 'not like', 'name' => '不包含'],
        ['exp' => '=', 'name' => '等于'],
        ['exp' => '<>', 'name' => '不等于'],
        ['exp' => '>', 'name' => '大于'],
        ['exp' => '>=', 'name' => '大于等于'],
        ['exp' => '<', 'name' => '小于'],
        ['exp' => '<=', 'name' => '小于等于'],
        ['exp' => 'between', 'name' => '在区间'],
        ['exp' => 'not between', 'name' => '不在区间'],
        ['exp' => 'in', 'name' => '在列表'],
        ['exp' => 'not in', 'name' => '不在列表'],
    ];

    if ($exp) {
        $exp = explode(',', $exp);
        foreach ($exps as $val) {
            foreach ($exp as $v) {
                if ($val['exp'] == $v) {
                    $tmp[] = $val;
                }
            }
        }
        return $tmp ?? [];
    }

    return $exps;
}

/**
 * 软删除更新数据
 *
 * @param array  $data  其它数据
 * @param string $field 排除字段
 *
 * @return array
 */
function delete_update($data = [], $field = '')
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
