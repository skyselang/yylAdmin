<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\admin;

use app\common\cache\admin\DatabaseCache;
use app\common\model\admin\DatabaseModel;
use app\common\utils\ByteUtils;
use app\common\utils\ServerUtils;
use app\common\utils\StringUtils;
use think\facade\Config;
use think\facade\Db;
use PDO;

/**
 * 数据库管理
 */
class DatabaseService
{
    /**
     * 备份列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param int    $is_extra 额外数据
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $is_extra = 0)
    {
        $model = new DatabaseModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',admin_user_id,username,file,size,table_num,remark,create_time';
        }
        $admin_super_hide_where = admin_super_hide_where();
        if ($admin_super_hide_where) {
            $where[] = $admin_super_hide_where;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $count = $model->where($where)->count($pk);
        $pages = ceil($count / $limit);
        $list = $model->field($field)->where($where)->page($page)->limit($limit)->order($order)->select()->toArray();

        foreach ($list as &$v) {
            $v['size'] = ByteUtils::format($v['size']);
        }

        $table = [];
        if ($is_extra) {
            $table = self::databseTable();
        }

        return compact('count', 'pages', 'page', 'limit', 'list', 'table');
    }

    /**
     * 备份信息
     *
     * @param int  $id   备份id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array
     */
    public static function info($id, $exce = true)
    {
        $info = DatabaseCache::get($id);
        if (empty($info)) {
            $model = new DatabaseModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('备份不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            $user = UserService::info($info['admin_user_id'], false);
            $info['username_now'] = $user['username'] ?? '';

            DatabaseCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 备份添加
     *
     * @param array $param 备份信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $model = new DatabaseModel();
        $pk = $model->getPk();

        $user = UserService::info(admin_user_id(), false);
        $table = self::realityTable($param['table']);
        $file = self::backup($table);

        $param['admin_user_id'] = $user['admin_user_id'] ?? 0;
        $param['username']      = $user['username'] ?? '';
        $param['path']          = $file['file_path'];
        $param['file']          = $file['file_name'];
        $param['size']          = $file['file_size'];
        $param['table']         = implode(',', $table);
        $param['table_num']     = count($table);
        $param['create_time']   = datetime();

        $id = $model->insertGetId($param);
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 备份修改
     *
     * @param mixed $ids    备份id
     * @param array $update 备份信息
     * 
     * @return array
     */
    public static function edit($ids, $update = [])
    {
        $model = new DatabaseModel();
        $pk = $model->getPk();

        unset($update[$pk], $update['ids']);

        $update['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($update);
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        DatabaseCache::del($ids);

        return $update;
    }

    /**
     * 备份删除
     *
     * @param array $ids  备份id
     * @param bool  $real 是否真实删除
     * 
     * @return array
     */
    public static function dele($ids, $real = false)
    {
        $model = new DatabaseModel();
        $pk = $model->getPk();

        if ($real) {
            $path = $model->where($pk, 'in', $ids)->column('path');
            $res = $model->where($pk, 'in', $ids)->delete();
            if ($res) {
                foreach ($path as $v) {
                    @unlink($v);
                }
            }
        } else {
            $update['is_delete']   = 1;
            $update['delete_time'] = datetime();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }
        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        DatabaseCache::del($ids);

        return $update;
    }

    /**
     * 备份下载
     *
     * @param int $id 备份id
     * 
     * @return array
     */
    public static function down($id)
    {
        $info = self::info($id);

        return $info;
    }

    /**
     * 备份还原
     *
     * @param int $id 备份id
     *
     * @return array
     */
    public static function restore($id)
    {
        $info = self::info($id);
        $path = $info['path'];
        $sql_file = $path;
        if (!is_file($sql_file)) {
            exception('文件不存在:' . $sql_file);
        }

        $errmsg = '';
        try {
            $filesize = filesize($sql_file);
            $query = Db::query('SELECT @@global.max_allowed_packet');
            if (isset($query[0]['@@global.max_allowed_packet']) && $filesize >= $query[0]['@@global.max_allowed_packet']) {
                Db::execute('SET @@global.max_allowed_packet = ' . ($filesize + 1024));
                // exception('备份文件超过配置max_allowed_packet大小，请修改Mysql服务器配置');
            }
            $sql = file_get_contents($sql_file);

            // 必须重连一次
            Db::connect(null, true)->query('select 1');
            Db::getPdo()->exec($sql);
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
        }

        if ($errmsg) {
            exception($errmsg);
        }
    }

    /**
     * 优化表
     *
     * @param array $table 表名
     *
     * @return array
     */
    public static function optimize($table)
    {
        $errmsg = '';
        try {
            foreach ($table as $v) {
                Db::execute('OPTIMIZE TABLE ' . $v);
            }
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
        }

        if ($errmsg) {
            exception($errmsg);
        }
    }

    /**
     * 修复表
     *
     * @param array $table 表名
     *
     * @return array
     */
    public static function repair($table)
    {
        $errmsg = '';
        try {
            foreach ($table as $v) {
                Db::execute('REPAIR TABLE ' . $v);
            }
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
        }

        if ($errmsg) {
            exception($errmsg);
        }
    }

    /**
     * 备份配置
     *
     * @return array
     */
    public static function getConfig()
    {
        $database = Config::get('database');
        $config = $database['connections'][$database['default']];

        return $config;
    }

    /**
     * 忽略的表
     *
     * @return array
     */
    public static function ignoreTable()
    {
        $config = self::getConfig();

        $ignore_table = Config::get('admin.database_ignore_table', []);
        foreach ($ignore_table as &$v) {
            $v = $config['prefix'] . $v;
        }
        $ignore_table[] = $config['prefix'] . 'admin_database';

        return $ignore_table;
    }

    /**
     * 备份的表
     * 
     * @param array $tables 备份表
     *
     * @return array
     */
    public static function realityTable($tables)
    {
        $ignore_table = self::ignoreTable();
        foreach ($tables as $kt => $vt) {
            if (in_array($vt, $ignore_table)) {
                unset($tables[$kt]);
            }
        }

        return $tables;
    }

    /**
     * 数据库表
     *
     * @return array
     */
    public static function databseTable()
    {
        $key = 'databseTable';
        $data = DatabaseCache::get($key);
        if (empty($data)) {
            $data = Db::query('show table status');
            $ignore_table = self::ignoreTable();
            foreach ($data as &$v) {
                $v['Max_data_length'] = ByteUtils::format($v['Max_data_length']);
                $v['Avg_row_length'] = ByteUtils::format($v['Avg_row_length']);
                $v['Index_length'] = ByteUtils::format($v['Index_length']);
                $v['Data_length'] = ByteUtils::format($v['Data_length']);
                $v['Data_free'] = ByteUtils::format($v['Data_free']);
                $v['is_ignore'] = 0;
                foreach ($ignore_table as $vi) {
                    if ($v['Name'] == $vi) {
                        $v['is_ignore'] = 1;
                    }
                }
            }
            DatabaseCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 数据库表信息
     * 
     * @param string $table_name 表名
     *
     * @return array
     */
    public static function tableInfo($table_name)
    {
        $key = $table_name;
        $data = DatabaseCache::get($key);
        if (empty($data)) {
            $data['info'] = [];
            $tables = self::databseTable();
            foreach ($tables as $table) {
                if ($table_name == $table['Name']) {
                    $data['info'] = $table;
                }
            }

            $create = Db::query('show create table ' . $table_name);
            $data['ddl'] = $create[0]['Create Table'];

            DatabaseCache::set($key, $data);
        }

        return $data;
    }

    /**
     * 数据库备份
     *
     * @param array $tables 备份的表
     *
     * @return array
     */
    public static function backup($tables)
    {
        $config = self::getConfig();
        $server = ServerUtils::server();
        $config['version'] = $server['mysql'];

        $db = new PDO('mysql:host=' . $config['hostname'] . ';dbname=' . $config['database'] . ';port=' . $config['hostport'], $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $db->exec('SET NAMES "utf8mb4"');

        # COUNT
        $ct = 0;
        # CONTENT
        $sqldump = '';
        # COPYRIGHT & OPTIONS
        $sqldump .= "/*\n";
        $sqldump .= " +----------------------------------------------------------------------\n";
        $sqldump .= " | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统\n";
        $sqldump .= " +----------------------------------------------------------------------\n";
        $sqldump .= " | Copyright https://gitee.com/skyselang All rights reserved\n";
        $sqldump .= " +----------------------------------------------------------------------\n";
        $sqldump .= " | Gitee: https://gitee.com/skyselang/yylAdmin\n";
        $sqldump .= " +----------------------------------------------------------------------\n";
        $sqldump .= "*/\n\n";

        $sqldump .= "/*\n";
        $sqldump .= " mysql: " . $config['version'] . "\n";
        $sqldump .= " hostname: " . $config['hostname'] . "\n";
        $sqldump .= " hostport：" . $config['hostport'] . "\n";
        $sqldump .= " database：" . $config['database'] . "\n";
        $sqldump .= " username：" . $config['username'] . "\n";
        $sqldump .= " datetime：" . datetime() . "\n";
        $sqldump .= "*/\n\n";

        $sqldump .= "SET NAMES utf8mb4;\n";
        $sqldump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        # LOOP: Get the tables
        foreach ($tables as $table) {
            # COUNT
            $ct++;
            # DATABASE: Count the rows in each tables
            $count_rows = $db->prepare("SELECT * FROM `" . $table . "`");
            $count_rows->execute();
            $c_rows = $count_rows->columnCount();
            # DATABASE: Count the columns in each tables
            $count_columns = $db->prepare("SELECT COUNT(*) FROM `" . $table . "`");
            $count_columns->execute();
            $c_columns = $count_columns->fetchColumn();
            # MYSQL DUMP: Remove tables if they exists
            $sqldump .= "-- ----------------------------\n";
            $sqldump .= "-- Table structure for " . $table . "\n";
            $sqldump .= "-- ----------------------------\n";
            $sqldump .= "DROP TABLE IF EXISTS `" . $table . "`;\n";
            # MYSQL DUMP: Create table if they do not exists
            # LOOP: Get the fields for the table
            foreach ($db->query("SHOW CREATE TABLE `" . $table . "`") as $field) {
                $sqldump .= str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $field['Create Table']);
            }
            # MYSQL DUMP: New rows
            $sqldump .= ";\n";
            # CHECK: There are one or more columns
            if ($c_columns != 0) {
                # MYSQL DUMP: List the data for each table
                $sqldump .= "-- ----------------------------\n";
                $sqldump .= "-- Records of " . $table . "\n";
                $sqldump .= "-- ----------------------------\n";
                # MYSQL DUMP: Insert into each table
                $sqldump .= "INSERT INTO `" . $table . "` (";
                # ARRAY
                $rows = [];
                $numeric = [];
                # LOOP: Get the tables
                foreach ($db->query("DESCRIBE `" . $table . "`") as $row) {
                    $rows[] = "`" . $row[0] . "`";
                    $numeric[] = (bool)preg_match('#^[^(]*(BYTE|COUNTER|SERIAL|INT|LONG$|CURRENCY|REAL|MONEY|FLOAT|DOUBLE|DECIMAL|NUMERIC|NUMBER)#i', $row[1]);
                }
                $sqldump .= implode(', ', $rows);
                $sqldump .= ") VALUES\n";
                # COUNT
                $c = 0;
                # LOOP: Get the tables
                foreach ($db->query("SELECT * FROM `" . $table . "`") as $data) {
                    # COUNT
                    $c++;
                    $sqldump .= "(";
                    # ARRAY
                    $cdata = [];
                    # LOOP
                    for ($i = 0; $i < $c_rows; $i++) {
                        $value = $data[$i];

                        if (is_null($value)) {
                            $cdata[] = "NULL";
                        } elseif ($numeric[$i]) {
                            $cdata[] = $value;
                        } else {
                            $cdata[] = $db->quote($value);
                        }
                    }
                    $sqldump .= implode(', ', $cdata);
                    $sqldump .= ")";
                    $sqldump .= ($c % 600 != 0 ? ($c_columns != $c ? ',' : ';') : '');
                    # CHECK
                    if ($c % 600 == 0) {
                        $sqldump .= ";\n\n";
                    } else {
                        $sqldump .= "\n";
                    }
                    # CHECK
                    if ($c % 600 == 0) {
                        $sqldump .= "INSERT INTO `" . $table . "`(";
                        # ARRAY
                        $rows = [];
                        # LOOP: Get the tables
                        foreach ($db->query("DESCRIBE `" . $table . "`") as $row) {
                            $rows[] = "`" . $row[0] . "`";
                        }
                        $sqldump .= implode(', ', $rows);
                        $sqldump .= ") VALUES\n";
                    }
                }
                $sqldump .= "\n";
            }
        }

        $sqldump .= "\n";
        $sqldump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $file_dir = '../runtime/database/' . date('Ymd');
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0755, true);
        }
        $file_name = $config['database'] . '-' . date('Ymd') . '-' . StringUtils::random() . '.sql';
        $file_path = $file_dir . '/' . $file_name;
        file_put_contents($file_path, $sqldump);
        $file_size = filesize($file_path);

        return ['file_path' => $file_path, 'file_name' => $file_name, 'file_size' => $file_size];
    }
}
