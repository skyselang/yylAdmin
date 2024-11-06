<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Filesystem;
use app\common\cache\member\TagCache;
use app\common\cache\member\MemberCache;
use app\common\service\file\ExportService as FileExportService;
use app\common\service\file\ImportService as FileImportService;
use app\common\model\member\TagModel;
use app\common\model\member\AttributesModel;

/**
 * 会员标签
 */
class TagService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'tag_id/d'   => '',
        'tag_name/s' => '',
        'tag_desc/s' => '',
        'remark/s'   => '',
        'sort/d'     => 250,
    ];

    /**
     * 会员标签列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * 
     * @return array ['count', 'pages', 'page', 'limit', 'list']
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new TagModel();
        $pk = $model->getPk();

        if (empty($field)) {
            $field = $pk . ',tag_name,tag_desc,remark,sort,is_disable,create_time,update_time';
        } else {
            $field = $pk . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $with = $append = $hidden = $field_no = [];
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
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
            $count_model = clone $model;
            $count = $count_model->where($where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list = $model->field($field)->where($where)
            ->with($with)->append($append)->hidden($hidden)
            ->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }

    /**
     * 会员标签信息
     *
     * @param int  $id   标签id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = TagCache::get($id);
        if (empty($info)) {
            $model = new TagModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('会员标签不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            TagCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员标签添加
     *
     * @param array $param 标签信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new TagModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();

        $model->save($param);
        $id = $model->$pk;
        if (empty($id)) {
            exception();
        }

        $param[$pk] = $id;

        return $param;
    }

    /**
     * 会员标签修改
     *
     * @param int|array $ids   标签id
     * @param array     $param 标签信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new TagModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $res = $model->where($pk, 'in', $ids)->update($param);
        if (empty($res)) {
            exception();
        }

        $param['ids'] = $ids;

        TagCache::del($ids);

        return $param;
    }

    /**
     * 会员标签删除
     *
     * @param array $ids  标签id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new TagModel();
        $pk = $model->getPk();

        if ($real) {
            $res = $model->where($pk, 'in', $ids)->delete();
        } else {
            $update = delete_update();
            $res = $model->where($pk, 'in', $ids)->update($update);
        }

        if (empty($res)) {
            exception();
        }

        $update['ids'] = $ids;

        TagCache::del($ids);

        return $update;
    }

    /**
     * 会员标签导出
     *
     * @param  array $param
     * @return array
     */
    public static function export($param)
    {
        $export = [
            'type'       => FileExportService::TYPE_MEMBER_TAG,
            'file_path'  => ExportService::$file_dir . '/member-tag-' . date('YmdHis') . '-' . uniqids() . '.xlsx',
            'file_name'  => '会员标签导出-' . date('Ymd-His') . '.xlsx',
            'param'      => ['where' => $param['where'], 'order' => $param['order']],
            'remark'     => $param['export_remark'] ?? '',
            'create_uid' => user_id(),
        ];
        $export_id = FileExportService::add($export);

        return ExportService::memberTag(['export_id' => $export_id]);
    }

    /**
     * 会员标签导入
     *
     * @param  array $param
     * @return array
     */
    public static function import($param)
    {
        $file_path = Filesystem::disk('public')
            ->putFile(ImportService::$file_dir, $param['import_file'], function () {
                return 'member-tag-' . date('YmdHis') . '-' . uniqids();
            });
        $import = [
            'type'       => FileImportService::TYPE_MEMBER_TAG,
            'file_name'  => $param['import_file']->getOriginalName(),
            'file_path'  => 'storage/' . $file_path,
            'file_size'  => $param['import_file']->getSize(),
            'remark'     => $param['import_remark'] ?? '',
            'create_uid' => user_id(),
        ];
        $import_id = FileImportService::add($import);

        return ImportService::memberTag(['import_id' => $import_id]);
    }

    /**
     * 会员标签会员列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * 
     * @return array 
     */
    public static function member($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        return MemberService::list($where, $page, $limit, $order, $field);
    }

    /**
     * 会员标签会员解除
     *
     * @param array $tag_id     标签id
     * @param array $member_ids 会员id
     *
     * @return int
     */
    public static function memberRemove($tag_id, $member_ids = [])
    {
        $where[] = ['tag_id', 'in', $tag_id];
        if (empty($member_ids)) {
            $member_ids = AttributesModel::where($where)->column('member_id');
        }
        $where[] = ['member_id', 'in', $member_ids];

        $res = AttributesModel::where($where)->delete();

        MemberCache::del($member_ids);

        return $res;
    }
}
