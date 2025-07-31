<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\file;

use hg\apidoc\annotation as Apidoc;
use think\facade\Filesystem;
use app\common\cache\file\FileCache as Cache;
use app\common\model\file\FileModel as Model;
use app\common\service\file\SettingService as FileSettingService;
use app\common\service\file\ExportService;
use app\common\service\file\StorageService;
use app\common\model\file\TagsModel;
use think\facade\Db;

/**
 * 文件管理
 */
class FileService
{
    /**
     * 缓存
     */
    public static function cache()
    {
        return new Cache();
    }

    /**
     * 模型
     */
    public static function model()
    {
        return new Model();
    }

    /**
     * 添加修改字段
     */
    public static $editField = [
        'file_id'     => '',
        'unique/s'    => '',
        'file_name/s' => '',
        'group_id/d'  => 0,
        'tag_ids/a'   => [],
        'file_type/s' => 'image',
        'domain/s'    => '',
        'remark/s'    => '',
        'sort/d'      => 250,
    ];

    /**
     * 批量修改字段
     */
    public static $updateField = ['unique', 'remark', 'sort', 'group_id', 'tag_ids', 'domain'];

    /**
     * 基础数据
     * @param bool $exp 是否返回查询表达式
     * @Apidoc\Returned("basedata", type="object", desc="基础数据", children={ 
     *   @Apidoc\Returned(ref="expsReturn"),
     *   @Apidoc\Returned("storages", type="array", desc="储存"),
     *   @Apidoc\Returned("file_types", type="array", desc="类型"),
     *   @Apidoc\Returned("add_types", type="array", desc="新增方式"),
     *   @Apidoc\Returned("settings", type="array", desc="设置"),
     *   @Apidoc\Returned("group", ref={GroupService::class,"info"}, type="array", desc="分组列表", field="group_id,group_unique,group_name"),
     *   @Apidoc\Returned("tag", ref={TagService::class,"info"}, type="array", desc="标签列表", field="tag_id,tag_unique,tag_name")
     * })
     */
    public static function basedata($exp = false)
    {
        $exps       = $exp ? where_exps() : [];
        $storages   = SettingService::storages('', true);
        $file_types = SettingService::fileTypes('', true);
        $add_types  = SettingService::addTypes('', true);
        $settings   = SettingService::info('limit_max,accept_ext');
        $groups     = GroupService::list([where_delete()], 0, 0, [], 'group_unique,group_name', false)['list'] ?? [];
        $tags       = TagService::list([where_delete()], 0, 0, [], 'tag_unique,tag_name', false)['list'] ?? [];

        return [
            'exps'       => $exps,
            'storages'   => $storages,
            'file_types' => $file_types,
            'add_types'  => $add_types,
            'settings'   => $settings,
            'groups'     => $groups,
            'tags'       => $tags
        ];
    }

    /**
     * 文件列表
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * @param array  $param 参数
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", children={
     *   @Apidoc\Returned(ref={Model::class}, field="file_id,unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_ext,file_size,sort,is_disable,create_time,update_time,delete_time"),
     *   @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name"),
     *   @Apidoc\Returned(ref={Model::class,"getGroupNameAttr"}, field="group_name"),
     *   @Apidoc\Returned(ref={Model::class,"getTagNamesAttr"}, field="tag_names"),
     *   @Apidoc\Returned(ref={Model::class,"getFileTypeNameAttr"}, field="file_type_name"),
     *   @Apidoc\Returned(ref={Model::class,"getFileSizeNameAttr"}, field="file_size_name"),
     *   @Apidoc\Returned(ref={Model::class,"getFileUrlAttr"}, field="file_url"),
     * })
     */
    public static function list($where = [], $page = 1, $limit = 10, $order = [], $field = '', $total = true, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($where)) {
            $where[] = where_delete();
        }
        if (empty($order)) {
            $order = ['update_time' => 'desc', $group => 'desc'];
        }
        if (empty($field)) {
            $field = $group . ',unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_ext,file_size,sort,is_disable,create_time,update_time,delete_time';
        } else {
            $field = $group . ',' . $field;
        }

        $wt = 'file_tags ';
        $wa = 'b';
        $model = $model->alias('a');
        $where_scope = [];
        foreach ($where as $wk => $wv) {
            if ($wv[0] === 'tag_ids') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.file_id=' . $wa . '.file_id');
                $where[$wk] = [$wa . '.tag_id', $wv[1], $wv[2]];
            } elseif ($wv[0] === 'tag_id') {
                $wa++;
                $model = $model->join($wt . $wa, 'a.file_id=' . $wa . '.file_id');
                $where_scope[] = [$wa . '.tag_id', $wv[1], $wv[2]];
                unset($where[$wk]);
            } elseif ($wv[0] === $pk) {
                $where[$wk] = ['a.' . $wv[0], $wv[1], $wv[2]];
            }
        }
        $where = array_values($where);

        if (($param['group_id'] ?? '') !== '') {
            $where_scope[] = ['group_id', '=', $param['group_id']];
        }
        if ($param['tag_ids'] ?? []) {
            $model = $model->join('file_tags ft', 'a.file_id=ft.file_id');
            $where_scope[] = ['ft.tag_id', 'in', $param['tag_ids']];
        }
        if (($param['storage'] ?? '') !== '') {
            $where_scope[] = ['storage', '=', $param['storage']];
        }
        if ($param['file_type'] ?? '') {
            $where_scope[] = ['file_type', '=', $param['file_type']];
        }
        if (($param['is_front'] ?? '') !== '') {
            $where_scope[] = ['is_front', '=', $param['is_front']];
        }
        if (($param['is_disable'] ?? '') !== '') {
            $where_scope[] = ['is_disable', '=', $param['is_disable']];
        }

        $with     = ['tags'];
        $append   = ['tag_names', 'file_url'];
        $hidden   = ['tags'];
        $field_no = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }
        if (strpos($field, 'group_id')) {
            $with[]   = $hidden[] = 'group';
            $append[] = 'group_name';
        }
        if (strpos($field, 'storage')) {
            $append[] = 'storage_name';
        }
        if (strpos($field, 'file_type')) {
            $append[] = 'file_type_name';
        }
        if (strpos($field, 'file_size')) {
            $append[] = 'file_size_name';
        }
        $fields = explode(',', $field);
        foreach ($fields as $k => $v) {
            if (in_array($v, $field_no)) {
                unset($fields[$k]);
            }
        }
        $field = implode(',', $fields);

        $count = $pages = 0;
        if ($total) {
            $count = model_where(clone $model, $where, $where_scope)->group($group)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $model = $model->field($field);
        $model = model_where($model, $where, $where_scope);
        $list  = $model->with($with)->append($append)->hidden($hidden)->order($order)->group($group)->select()->toArray();

        return ['count' => $count, 'pages' => $pages, 'page' => $page, 'limit' => $limit, 'list' => $list];
    }

    /**
     * 文件信息
     * @param string $id   文件id、编号
     * @param bool   $exce 不存在是否抛出异常
     * @return array
     * @Apidoc\Query(ref={Model::class}, field="file_id")
     * @Apidoc\Returned(ref={Model::class})
     * @Apidoc\Returned(ref={Model::class,"getIsDisableNameAttr"}, field="is_disable_name")
     * @Apidoc\Returned(ref={Model::class,"getGroupNameAttr"}, field="group_name")
     * @Apidoc\Returned(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Returned(ref={Model::class,"getTagNamesAttr"}, field="tag_names")
     * @Apidoc\Returned(ref={Model::class,"getFileTypeNameAttr"}, field="file_type_name")
     * @Apidoc\Returned(ref={Model::class,"getFileSizeNameAttr"}, field="file_size_name")
     * @Apidoc\Returned(ref={Model::class,"getFileUrlAttr"}, field="file_url")
     */
    public static function info($id, $exce = true)
    {
        $cache = self::cache();
        $info  = $cache->get($id);
        if (empty($info)) {
            $model = self::model();
            $pk    = $model->getPk();

            if (is_numeric($id)) {
                $where[] = [$pk, '=', $id];
            } else {
                $where[] = ['unique', '=', $id];
                $where[] = where_delete();
            }

            $info = $model->where($where)->find();
            if (empty($info)) {
                if ($exce) {
                    exception(lang('文件不存在：') . $id);
                }
                return [];
            }
            $info = $info->append(['is_disable_name', 'group_name', 'tag_ids', 'tag_names', 'file_type_name', 'file_url', 'file_size'])
                ->hidden(['group', 'tags'])
                ->toArray();

            $cache->set($id, $info);
        }

        return $info;
    }

    /**
     * 文件添加
     * @param array $param 文件信息
     * @Apidoc\Param("add_type", type="string", default="add", desc="新增方式，upload上传，add添加")
     * @Apidoc\Param("file_url", type="string", require=true, desc="文件链接, 添加（add_type=add）时必传", mock="@image")
     * @Apidoc\Param("file", type="file", require=true, default="", desc="文件, 上传（add_type=upload）时必传")
     * @Apidoc\Param(ref={Model::class}, field="group_id,file_type,remark,sort")
     * @Apidoc\Param(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public static function add($param)
    {
        if (isset($param['tag_ids']) && is_string($param['tag_ids'])) {
            if ($param['tag_ids']) {
                $param['tag_ids'] = explode(',', $param['tag_ids']);
            } else {
                $param['tag_ids'] = [];
            }
        }

        $model = self::model();
        $pk    = $model->getPk();

        $datetime = datetime();
        $add_type = $param['add_type'] ?? 'upload';
        if ($add_type === 'add') {
            $file_info  = self::fileInfo($param['file_url']);
            $file_where = [['domain', '=', $file_info['domain']], ['file_path', '=', $file_info['file_path']]];
            $file_exist = $model->field($pk)->where($file_where)->find();
            if (empty($file_exist)) {
                if (empty($file_info['file_ext'])) {
                    unset($file_info['file_type']);
                }
                $param = array_merge($param, $file_info);
            }
        } else {
            $file = $param['file'] ?? null;
            if ($file) {
                unset($param['file']);

                $file_md5  = $file->hash('md5');
                $file_hash = $file->hash('sha1');
                $file_size = $file->getSize();
                $file_ext  = strtolower($file->getOriginalExtension());
                $file_type = SettingService::fileType($file_ext);
                $file_name = mb_substr($file->getOriginalName(), 0, - (mb_strlen($file_ext) + 1));
                $file_path = Filesystem::disk('public')
                    ->putFile('file', $file, function () use ($file_hash) {
                        return $file_hash;
                    });

                $param['file_name'] = $file_name;
                $param['file_md5']  = $file_md5;
                $param['file_hash'] = $file_hash;
                $param['file_path'] = 'storage/' . $file_path;
                $param['file_ext']  = $file_ext;
                $param['file_size'] = $file_size;
                $param['file_type'] = $file_type;
            }

            $file_exist = $model->field($pk)->where('file_hash', $param['file_hash'])->find();
            if ($file_exist) {
                $file_exist = $file_exist->toArray();
                $param[$pk] = $file_exist[$pk];
            } else {
                $param[$pk] = '';
            }

            // 对象存储
            $param = StorageService::upload($param);
        }

        if ($file_exist) {
            unsets($param, ['unique', 'file_name', 'group_id', 'tag_ids', 'remark', 'sort']);
            $param['is_disable'] = 0;
            $param['is_delete']  = 0;
            $id = $file_exist[$pk];
            self::edit($id, $param);
        } else {
            unset($param[$pk]);
            if (empty($param['unique'] ?? '')) {
                $param['unique'] = uniqids();
            }
            $param['create_uid']  = user_id();
            $param['create_time'] = $datetime;
            $param['update_time'] = $datetime;
            $model->save($param);
            // 添加标签
            if (isset($param['tag_ids']) && $param['tag_ids']) {
                $model->tags()->saveAll($param['tag_ids']);
            }
            $id = $model->$pk;
            if (empty($id)) {
                exception();
            }
        }

        $info = self::info($id);

        return $info;
    }

    /**
     * 文件修改
     * @param int|array $ids   文件id
     * @param array     $param 文件信息
     * @Apidoc\Query(ref={Model::class})
     * @Apidoc\Param(ref={Model::class}, field="file_id,file_name,group_id,file_type,domain,remark,sort")
     * @Apidoc\Param(ref={Model::class,"getTagIdsAttr"}, field="tag_ids")
     */
    public static function edit($ids, $param = [])
    {
        $model = self::model();
        $pk    = $model->getPk();

        unset($param[$pk], $param['ids']);
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
            $model->where($pk, 'in', $ids)->update($param);
            if (var_isset($param, ['tag_ids'])) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 修改标签
                    if (isset($param['tag_ids'])) {
                        $info = $info->append(['tag_ids']);
                        model_relation_update($info, $info['tag_ids'], $param['tag_ids'], 'tags');
                    }
                }
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $param;
    }

    /**
     * 文件删除
     * @param array $ids  文件id
     * @param bool  $real 是否真实删除
     * @Apidoc\Param(ref="idsParam")
     */
    public static function dele($ids, $real = false)
    {
        $model = self::model();
        $pk    = $model->getPk();

        $unique = $model->where($pk, 'in', $ids)->column('unique');

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                $file = $model->field($pk . ',storage,file_path')->where($pk, 'in', $ids)->select();
                foreach ($file as $v) {
                    $info = $model->find($v[$pk]);
                    // 删除标签
                    $info->tags()->detach();
                    // 删除文件
                    try {
                        unlink($v['file_path']);
                    } catch (\Exception $e) {
                    }
                }
                $model->where($pk, 'in', $ids)->delete();
                StorageService::dele($file);
            } else {
                $update = update_softdele();
                $model->where($pk, 'in', $ids)->update($update);
            }
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }
        if (isset($errmsg)) {
            exception($errmsg);
        }

        $update['ids'] = $ids;

        $cache = self::cache();
        $cache->del($ids);
        $cache->del($unique);

        return $update;
    }

    /**
     * 文件是否禁用
     * @param array $ids        id
     * @param int   $is_disable 是否禁用
     * @Apidoc\Param(ref="disableParam")
     */
    public static function disable($ids, $is_disable)
    {
        $data = self::edit($ids, ['is_disable' => $is_disable]);

        return $data;
    }

    /**
     * 文件批量修改
     * @param array  $ids   id
     * @param string $field 字段
     * @param mixed  $value 值
     * @Apidoc\Param(ref="updateParam")
     */
    public static function update($ids, $field, $value)
    {
        $model = self::model();

        if ($field == 'unique') {
            $data = update_unique($model, $ids, $field, $value, __CLASS__);
        } elseif ($field == 'sort') {
            $data = update_sort($model, $ids, $field, $value, __CLASS__);
        } else {
            $data = self::edit($ids, [$field => $value]);
        }

        return $data;
    }


    /**
     * 文件导出导入表头
     * @param string $exp_imp export导出，import导入
     */
    public static function header($exp_imp = 'import')
    {
        $model = self::model();
        $pk    = $model->getPk();
        $is_disable = $exp_imp == 'export' ? 'is_disable_name' : 'is_disable';
        $storage = $exp_imp == 'export' ? 'storage_name' : 'storage';
        $file_type = $exp_imp == 'export' ? 'file_type_name' : 'file_type';
        $file_size = $exp_imp == 'export' ? 'file_size_name' : 'file_size';
        $group_id = $exp_imp == 'export' ? 'group_name' : 'group_id';
        $tag_ids = $exp_imp == 'export' ? 'tag_names' : 'tag_ids';
        // index下标，field字段，name名称，width宽度，color颜色，type类型
        $header = [
            ['field' => $pk, 'name' => lang('ID'), 'width' => 10],
            ['field' => 'unique', 'name' => lang('编号'), 'width' => 22],
            // ['field' => 'file_hash', 'name' => lang('哈希'), 'width' => 16],
            ['field' => 'file_url', 'name' => lang('文件'), 'width' => 30, 'color' => 'FF0000'],
            // ['field' => $file_type, 'name' => lang('类型'), 'width' => 8],
            ['field' => 'file_name', 'name' => lang('名称'), 'width' => 30],
            // ['field' => 'file_ext', 'name' => lang('后缀'), 'width' => 8],
            // ['field' => $file_size, 'name' => lang('大小'), 'width' => 10],
            ['field' => $group_id, 'name' => lang('分组'), 'width' => 30],
            ['field' => $tag_ids, 'name' => lang('标签'), 'width' => 30],
            // ['field' => $storage, 'name' => lang('存储'), 'width' => 16],
            // ['field' => 'file_path', 'name' => lang('路径'), 'width' => 20],
            ['field' => 'remark', 'name' => lang('备注'), 'width' => 20],
            ['field' => $is_disable, 'name' => lang('禁用'), 'width' => 10],
            ['field' => 'sort', 'name' => lang('排序'), 'width' => 10],
            ['field' => 'create_time', 'name' => lang('添加时间'), 'width' => 22, 'type' => 'time'],
            ['field' => 'update_time', 'name' => lang('修改时间'), 'width' => 22, 'type' => 'time'],
        ];

        // 生成下标
        foreach ($header as $index => &$value) {
            $value['index'] = $index;
        }
        if ($exp_imp == 'import') {
            $header[] = ['index' => -1, 'field' => 'result_msg', 'name' => lang('导入结果'), 'width' => 60];
        }

        return $header;
    }

    /**
     * 文件导出
     * @param array $export_info 导出信息
     * @Apidoc\Query(ref="exportParam")
     * @Apidoc\Param(ref="exportParam")
     * @Apidoc\Returned(ref={ExportService::class,"info"})
     */
    public static function export($export_info)
    {
        $export_info['is_tree'] = 0;
        $export_info['type']    = FileSettingService::EXPIMP_TYPE_FILE;

        $field = '';
        $limit = 10000;
        $data  = ExportService::exports(__CLASS__, $export_info, $field, $limit);

        return $data;
    }

    /**
     * 文件导入
     * @param array $import_info 导入信息
     * @param bool  $is_add      是否添加导入信息
     * @Apidoc\Query(ref="importParam")
     * @Apidoc\Param(ref="importParam")
     * @Apidoc\Returned(ref="importParam")
     * @Apidoc\Returned(ref={ImportService::class,"info"})
     */
    public static function import($import_info, $is_add = false)
    {
        if ($is_add) {
            $import_info['type'] = FileSettingService::EXPIMP_TYPE_FILE;
            $import_id = ImportService::add($import_info);
            $data = ImportService::imports($import_id, __CLASS__, __FUNCTION__);
            return $data;
        }

        $header = self::header('import');
        $import = ImportService::importsReader($header, $import_info['file_path']);
        $model = self::model();
        $table = $model->getTable();
        $pk = $model->getPk();
        $import_num = count($import);
        $success = $fail = [];
        $datetime = datetime();
        $batch_num = 10000;

        while (count($import) > 0) {
            $batchs = array_splice($import, 0, $batch_num);
            foreach ($batchs as $key => $val) {
                $temp = [];
                foreach ($header as $vh) {
                    if ($vh['index'] > -1) {
                        $temp[$vh['field']] = $val[$vh['index']] ?? '';
                    }
                }
                $batchs[$key] = $temp;
            }

            $ids = array_column($batchs, $pk);
            $uniques = array_column($batchs, 'unique');
            $ids_repeat = array_repeat($ids);
            $uniques_repeat = array_repeat($uniques);
            $ids = Db::table($table)->where($pk, '>', 0)->where($pk, 'in', $ids)->where('is_delete', 0)->column($pk);
            $uniques = Db::table($table)->where($pk, 'not in', $ids)->where('unique', 'in', $uniques)
                ->where('is_delete', 0)->column('unique');

            $updates = $inserts = [];
            foreach ($batchs as $batch) {
                $batch['result_msg'] = [];
                if ($batch[$pk]) {
                    if (filter_var($batch[$pk], FILTER_VALIDATE_INT) === false) {
                        $batch['result_msg'][] = lang('ID只能是整数');
                    } elseif (in_array($batch[$pk], $ids_repeat)) {
                        $batch['result_msg'][] = lang('ID重复');
                    } elseif (!$import_info['is_update'] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'][] = lang('ID已存在');
                    }
                }
                if ($batch['unique']) {
                    if (is_numeric($batch['unique'])) {
                        $batch['result_msg'][] = lang('编号不能为纯数字');
                    } elseif (in_array($batch['unique'], $uniques_repeat)) {
                        $batch['result_msg'][] = lang('编号重复');
                    } elseif (in_array($batch['unique'], $uniques)) {
                        $batch['result_msg'][] = lang('编号已存在');
                    }
                }
                if ($batch['file_url']) {
                    if (filter_var($batch['file_url'], FILTER_VALIDATE_URL) === false) {
                        $batch['result_msg'][] = lang('文件必须是有效url');
                    }
                }
                if ($batch['create_time']) {
                    if (!strtotime($batch['create_time'])) {
                        $batch['result_msg'][] = lang('添加时间格式错误');
                    }
                }
                if ($batch['update_time']) {
                    if (!strtotime($batch['update_time'])) {
                        $batch['result_msg'][] = lang('修改时间格式错误');
                    }
                }

                if ($batch['result_msg']) {
                    $batch['result_msg'] = lang('失败：') . implode('，', $batch['result_msg']);
                    $fail[] = $batch;
                } else {
                    $batch['result_msg'] = lang('成功：');
                    $batch_tmp = $batch;
                    $batch_tmp['is_disable'] = (in_array($batch['is_disable'], ['1', lang('是')])) ? 1 : 0;
                    $batch_tmp['file_id'] = self::fileId($batch['file_url']);
                    $batch_tmp['group_id'] = GroupService::nameId($batch['group_id']);
                    $batch_tmp['tag_ids'] = TagService::nameId($batch['tag_ids']);
                    unset($batch_tmp['file_url'], $batch_tmp['file_type'], $batch_tmp['file_size'], $batch_tmp['storage']);
                    if ($batch[$pk] && in_array($batch[$pk], $ids)) {
                        $batch['result_msg'] .= lang('修改');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? null : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? $datetime : $batch['update_time'];
                        $updates[] = $batch_tmp;
                    } else {
                        $batch['result_msg'] .= lang('添加');
                        $batch_tmp['create_time'] = empty($batch['create_time']) ? $datetime : $batch['create_time'];
                        $batch_tmp['update_time'] = empty($batch['update_time']) ? null : $batch['update_time'];
                        unset($batch_tmp[$pk]);
                        $inserts[] = $batch_tmp;
                    }
                    $success[] = $batch;
                }
            }
            unset($batchs, $uniques);

            $attr_adds = [];
            if ($updates) {
                foreach ($updates as $key => $update) {
                    if ($update['tag_ids']) {
                        foreach ($update['tag_ids'] as $tag_id) {
                            $attr_adds[] = [$pk => $update[$pk], 'tag_id' => $tag_id];
                        }
                    }
                    unset($update['tag_ids']);
                    $updates[$key] = $update;
                }
            }
            if ($inserts) {
                foreach ($inserts as $key => $insert) {
                    $insert_tmp = $insert;
                    unset($insert_tmp['tag_ids']);
                    $id = Db::table($table)->insertGetId($insert_tmp);
                    if ($insert['tag_ids']) {
                        foreach ($insert['tag_ids'] as $tag_id) {
                            $attr_adds[] = [$pk => $id, 'tag_id' => $tag_id];
                        }
                    }
                }
            }
            $batch_header = $header;
            foreach ($batch_header as $key => $val) {
                if (in_array($val['field'], ['tag_ids', 'file_url', 'file_type', 'file_size', 'storage'])) {
                    unset($batch_header[$key]);
                }
            }
            $attr_del_ids = array_column($updates, $pk);
            batch_update($model, $batch_header, $updates);
            self::deleTagAttr($attr_del_ids);
            if ($attr_adds) {
                TagsModel::insertAll($attr_adds);
            }

            $cache = self::cache();
            $cache->clear();

            unset($updates, $inserts);
        }
        unset($import);

        return ['import_num' => $import_num, 'header' => $header, 'success' => $success, 'fail' => $fail];
    }

    /**
     * 文件上/下一个
     * @param int    $id    文件id
     * @param string $type  prev上一个，next下一个
     * @param array  $where 文件条件
     * @return array 文件
     */
    public static function prevNext($id, $type = 'prev', $where = [])
    {
        if ($type == 'next') {
            $where[] = ['a.file_id', '>', $id];
            $order = ['a.file_id' => 'asc'];
        } else {
            $where[] = ['a.file_id', '<', $id];
            $order = ['a.file_id' => 'desc'];
        }
        $where[] = where_disable();
        $where[] = where_delete();

        $field = 'unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_ext';

        $info = self::list($where, 0, 1, $order, $field)['list'];

        return $info[0] ?? [];
    }

    /**
     * 文件统计
     * @Apidoc\Returned("count", type="int", desc="文件总数")
     * @Apidoc\Returned("data", type="array", desc="图表series.data")
     * @return array
     */
    public static function statistic()
    {
        $key = 'statistic' . lang_get();
        $cache = self::cache();
        $data = $cache->get($key);
        if (empty($data)) {
            $model = self::model();

            $file_types = SettingService::fileTypes('', true);
            $file_field = 'file_type,count(file_type) as count';
            $file_count = $model->field($file_field)->where([where_delete()])->group('file_type')->select()->toArray();
            foreach ($file_types as $v) {
                $temp = [];
                $temp['name']  = lang($v['label']);
                $temp['value'] = 0;
                foreach ($file_count as $vfc) {
                    if ($v['value'] == $vfc['file_type']) {
                        $temp['value'] = $vfc['count'];
                    }
                }
                $data['data'][] = $temp;
            }
            $data['count'] = $model->where([where_delete()])->count();

            $cache->set($key, $data);
        }

        return $data;
    }

    /**
     * 文件id
     * @param string $file_url 文件URL
     * @return int 文件id
     */
    public static function fileId($file_url)
    {
        $file_id = 0;
        if (filter_var($file_url, FILTER_VALIDATE_URL) !== false) {
            $model = self::model();
            $pk    = $model->getPk();

            $file_info  = self::fileInfo($file_url);
            $file_where = [['domain', '=', $file_info['domain']], ['file_path', '=', $file_info['file_path']]];
            $file_exist = $model->field($pk)->where($file_where)->find();

            if (empty($file_exist)) {
                $file_info  = self::fileDown($file_url);
                $file_where = [['file_hash', '=', $file_info['file_hash']]];
                $file_exist = $model->field($pk)->where($file_where)->find();
            }

            if (empty($file_exist)) {
                $file_info['add_type'] = 'upload';
                $file_info = self::add($file_info);
                $file_exist[$pk] = $file_info[$pk];
            }

            $file_id   = $file_exist[$pk] ?? 0;
            $file_info = self::info($file_id, false);
            if ($file_info['is_delete']) {
                self::edit($file_id, ['is_delete' => 0]);
            }
        }

        return $file_id;
    }

    /**
     * 文件信息
     * @param string $file_url 文件URL
     * @return array 文件信息 [domain,file_name,file_path,file_ext,file_type]
     */
    public static function fileInfo($file_url)
    {
        $file   = parse_url($file_url);
        $scheme = $file['scheme'] ?? '';
        $port   = $file['port'] ?? '';
        $host   = $file['host'] ?? '';
        $path   = $file['path'] ?? '';
        $query  = $file['query'] ?? '';

        $file_info = [];
        $file_info['domain'] = $scheme . '://' . $host . ($port ? ':' . $port : '');
        if ($file_info['domain'] === server_url()) {
            $file_info['domain'] = '';
        }
        $file_info['file_path'] = ltrim($path . ($query ? '?' . $query : ''), '/');
        $file_info['file_ext']  = substr(strrchr($path, '.'), 1);
        $len = strlen($file_info['file_ext']);
        for ($i = 0; $i < $len; $i++) {
            if (!ctype_alpha($file_info['file_ext'][$i])) {
                $file_info['file_ext'] = substr($file_info['file_ext'], 0, $i);
                break;
            }
        }
        $file_info['file_name'] = trim(substr(strrchr($path, '/'), 0, - (strlen($file_info['file_ext']) + 1)), '/');
        $file_info['file_type'] = SettingService::fileType($file_info['file_ext']);

        return $file_info;
    }

    /**
     * 文件下载
     * @param string $file_url 文件URL
     * @return array 文件信息 [file_url,domain,file_md5,file_hash,file_name,file_path,file_ext,file_type,file_size]
     */
    public static function fileDown($file_url)
    {
        // 验证文件URL
        if (!filter_var($file_url, FILTER_VALIDATE_URL)) {
            exception(lang('文件URL无效: ') . $file_url);
        }

        // 确保保存目录存在
        $saveDirectory = 'storage/file';
        if (!file_exists($saveDirectory)) {
            mkdir($saveDirectory, 0755, true);
        }

        // 获取原始文件名和扩展名
        $originalName = basename(parse_url($file_url, PHP_URL_PATH));
        $extension    = pathinfo($originalName, PATHINFO_EXTENSION);

        // 获取临时文件名
        $tempFile = tempnam(sys_get_temp_dir(), 'filedown_');

        // 使用 cURL 下载文件到临时文件
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FILE, fopen($tempFile, 'w+'));

        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$success || $httpCode !== 200) {
            unlink($tempFile);
            exception(lang('文件下载失败: ') . $file_url);
        }

        // 计算文件信息
        $fileInfo    = self::fileInfo($file_url);
        $file_domain = $fileInfo['domain'];
        if ($file_domain == server_url()) {
            $file_domain = '';
        }
        $fileMd5  = md5_file($tempFile);
        $fileHash = hash_file('sha1', $tempFile);
        $fileName = $fileHash;
        $fileSize = filesize($tempFile);
        $fileType = SettingService::fileType($extension);

        // 确定最终文件名
        $finalFilename = $fileHash;
        $finalPath = rtrim($saveDirectory, '/') . '/' . $finalFilename;

        $file_info = [
            'file_url'  => file_url($finalPath),
            'domain'    => $file_domain,
            'file_md5'  => $fileMd5,
            'file_hash' => $fileHash,
            'file_name' => $fileName,
            'file_path' => $finalPath,
            'file_ext'  => $extension,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ];

        // 检查是否已存在相同哈希的文件
        if (file_exists($finalPath)) {
            unlink($tempFile);
            return $file_info;
        }

        // 移动临时文件到最终位置
        if (!rename($tempFile, $finalPath)) {
            unlink($tempFile);
            exception(lang('文件保存失败: ') . $finalPath);
        }

        return $file_info;
    }

    /**
     * 删除关联标签
     * @param array $ids id
     * @return int
     */
    public static function deleTagAttr($ids)
    {
        if (empty($ids)) {
            return 0;
        }
        $model     = self::model();
        $pk        = $model->getPk();
        $tag_model = new TagsModel();
        $tag_pk    = $tag_model->getPk();
        $res       = TagsModel::where($pk, 'in', $ids)->where($tag_pk, '>', 0)->delete();
        return $res;
    }
}
