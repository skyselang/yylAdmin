<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\file;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\api\middleware\FileSettingMiddleware;
use app\common\validate\file\FileValidate;
use app\common\service\file\SettingService;
use app\common\service\file\FileService;
use app\common\service\file\GroupService;
use app\common\service\file\TagService;

/**
 * @Apidoc\Title("lang(文件)")
 * @Apidoc\Group("file")
 * @Apidoc\Sort("90")
 */
class File extends BaseController
{
    /**
     * 控制器中间件
     */
    protected $middleware = [FileSettingMiddleware::class];

    /**
     * @Apidoc\Title("lang(分组列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", ref={GroupService::class,"info"}, field="group_id,group_unique,group_name", desc="分组列表")
     */
    public function group()
    {
        $setting = SettingService::info();

        $where = where_disable(['group_id', 'in', $setting['api_file_group_ids']]);

        $data = GroupService::list($where, $this->page(0), $this->limit(0), $this->order(), 'group_unique,group_name', false);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(标签列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", ref={TagService::class,"info"}, field="tag_id,tag_unique,tag_name", desc="标签列表")
     */
    public function tag()
    {
        $setting = SettingService::info();

        $where = where_disdel(['tag_id', 'in', $setting['api_file_tag_ids']]);

        $data = TagService::list($where, $this->page(0), $this->limit(0), $this->order(), 'tag_unique,tag_name', false);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件列表)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref={GroupService::class,"edit"}, field="group_id,file_type")
     * @Apidoc\Query(ref={TagService::class,"edit"}, field="tag_id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="文件列表", children={
     *   @Apidoc\Returned(ref={FileService::class,"info"}, field="file_id,unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_ext,file_size,sort,file_url,group_name,tag_names"),
     * })
     * @Apidoc\Returned("group", type="array", ref={GroupService::class,"info"}, field="group_id,group_unique,group_name", desc="分组列表")
     * @Apidoc\Returned("tag", type="array", ref={TagService::class,"info"}, field="tag_id,tag_unique,tag_name", desc="标签列表")
     */
    public function list()
    {
        $file_type = $this->param('file_type/s', '');
        $group_id  = $this->param('group_id/s', '');
        $tag_id    = $this->param('tag_id/s', '');

        $setting = SettingService::info('file_types,api_file_types,api_file_group_ids,api_file_tag_ids');
        $file_types = [];
        foreach ($setting['api_file_types'] as $val) {
            foreach ($setting['file_types'] as $k => $v) {
                if ($k == $val) {
                    $file_types[$k] = $v;
                }
            }
        }
        $setting['file_types'] = $file_types;

        $where_base = [where_disable(), where_delete()];
        $where = $where_base;
        if ($file_type) {
            if (!in_array($file_type, $setting['api_file_types'])) {
                $file_type = '-1';
            }
            $where[] = ['file_type', 'in', $file_type];
        } else {
            $where[] = ['file_type', 'in', $setting['api_file_types']];
        }
        if ($group_id) {
            if (!in_array($group_id, $setting['api_file_group_ids'])) {
                $group_id = -1;
            }
            $where[] = ['group_id', 'in', $group_id];
        } else {
            $where[] = ['group_id', 'in', $setting['api_file_group_ids']];
        }
        if ($tag_id) {
            if (!in_array($tag_id, $setting['api_file_tag_ids'])) {
                $tag_id = -1;
            }
            $where[] = ['tag_ids', 'in', [$tag_id]];
        } else {
            $where[] = ['tag_ids', 'in', $setting['api_file_tag_ids']];
        }

        $order = ['sort' => 'desc', 'file_id' => 'desc'];
        $field = 'unique,group_id,storage,domain,file_type,file_hash,file_name,file_path,file_ext,file_size,sort';
        $data = FileService::list($where, $this->page(), $this->limit(), $this->order($order), $field);
        $data['setting'] = $setting;

        $where_group = $where_base;
        $where_group[] = ['group_id', 'in', $setting['api_file_group_ids']];
        $data['group'] = GroupService::list($where_group, 0, 0, [], 'group_unique,group_name', false)['list'] ?? [];

        $where_tag = $where_base;
        $where_tag[] = ['tag_id', 'in', $setting['api_file_tag_ids']];
        $data['tag'] = TagService::list($where_tag, 0, 0, [], 'tag_unique,tag_name', false)['list'] ?? [];

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(文件信息)")
     * @Apidoc\Query("file_id", type="string", require=true, default="", desc="文件id、编号")
     * @Apidoc\Returned(ref={FileService::class,"info"})
     * @Apidoc\Returned("prev_info", type="object", desc="上一条", children={
     *   @Apidoc\Returned(ref={FileService::class,"info"})
     * })
     * @Apidoc\Returned("next_info", type="object", desc="下一条", children={
     *   @Apidoc\Returned(ref={FileService::class,"info"})
     * })
     */
    public function info()
    {
        $param = $this->params(['file_id/s' => '']);

        validate(FileValidate::class)->scene('info')->check($param);

        $data = FileService::info($param['file_id'], false);
        $setting = SettingService::info('api_file_types,api_file_group_ids,api_file_tag_ids');
        $file_type = $data['file_type'] ?? '';
        if (!in_array($file_type, $setting['api_file_types'])) {
            $file_type = '';
        }
        $group_id = $data['group_id'] ?? '';
        if (!in_array($group_id, $setting['api_file_group_ids'])) {
            $group_id = 0;
        }
        $tag_id = $data['tag_ids'] ?? [];
        if (!array_intersect($tag_id, $setting['api_file_tag_ids'])) {
            $tag_id = 0;
        }
        if (empty($data) || $data['is_disable'] || $data['is_delete'] || !$file_type || !$group_id || !$tag_id) {
            return error(lang('文件不存在'));
        }

        $where = [where_disable(), where_delete()];
        $where[] = ['file_type', 'in', $setting['api_file_types']];
        $where[] = ['group_id', 'in', $setting['api_file_group_ids']];
        $where[] = ['tag_ids', 'in', $setting['api_file_tag_ids']];
        $prev_info = FileService::prevNext($data['file_id'], 'prev', $where);
        $next_info = FileService::prevNext($data['file_id'], 'next', $where);
        $data['prev_info'] = $prev_info;
        $data['next_info'] = $next_info;

        return success($data);
    }
}
