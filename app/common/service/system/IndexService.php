<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use think\facade\Db;
use app\common\cache\Cache;
use app\common\service\system\UserService;
use hg\apidoc\annotation as Apidoc;

/**
 * 控制台
 */
class IndexService
{
    /**
     * 首页
     *
     * @return array
     */
    public static function index()
    {
        $data['name']   = 'yylAdmin';
        $data['desc']   = '基于ThinkPHP8和Vue3的极简后台管理系统';
        $data['gitee']  = 'https://gitee.com/skyselang/yylAdmin';
        $data['github'] = 'https://github.com/skyselang/yylAdmin';

        return $data;
    }

    /**
     * 总数统计
     * @Apidoc\Returned("count", type="array", children={
     *   @Apidoc\Returned("name", type="string", desc="名称"),
     *   @Apidoc\Returned("count", type="int", desc="总数")
     * })
     * @return array
     */
    public static function count()
    {
        $uid  = user_id();
        $key  = 'statistic:count' . $uid . lang_get();
        $data = Cache::get($key);
        if (empty($data)) {
            $count = [];
            $table = [
                ['table' => 'member', 'name' => lang('member.member'), 'menu_url' => 'admin/member.Member/list'],
                // ['table' => 'member_tag', 'name' => '会员标签', 'menu_url' => 'admin/member.Tag/list'],
                // ['table' => 'member_group', 'name' => '会员分组', 'menu_url' => 'admin/member.Group/list'],
                // ['table' => 'member_api', 'name' => '会员接口', 'menu_url' => 'admin/member.Api/list'],
                ['table' => 'content', 'name' => lang('content.content'), 'menu_url' => 'admin/content.Content/list'],
                // ['table' => 'content_category', 'name' => '内容分类', 'menu_url' => 'admin/content.Category/list'],
                // ['table' => 'content_tag', 'name' => '内容标签', 'menu_url' => 'admin/content.Tag/list'],
                ['table' => 'file', 'name' => lang('file.file'), 'menu_url' => 'admin/file.File/list'],
                // ['table' => 'file_group', 'name' => '文件分组', 'menu_url' => 'admin/file.Group/list'],
                // ['table' => 'file_tag', 'name' => '文件标签', 'menu_url' => 'admin/file.Tag/list'],
                ['table' => 'setting_carousel', 'name' => lang('setting.carousel'), 'menu_url' => 'admin/setting.Carousel/list'],
                ['table' => 'setting_notice', 'name' => lang('setting.notice'), 'menu_url' => 'admin/setting.Notice/list'],
                ['table' => 'setting_accord', 'name' => lang('setting.accord'), 'menu_url' => 'admin/setting.Accord/list'],
                ['table' => 'setting_feedback', 'name' => lang('setting.feedback'), 'menu_url' => 'admin/setting.Feedback/list'],
                ['table' => 'setting_region', 'name' => lang('setting.region'), 'menu_url' => 'admin/setting.Region/list'],
                // ['table' => 'setting_link', 'name' => '友链', 'menu_url' => 'admin/setting.Link/list'],
                // ['table' => 'system_menu', 'name' => '菜单', 'menu_url' => 'admin/system.Menu/list'],
                // ['table' => 'system_role', 'name' => '角色', 'menu_url' => 'admin/system.Role/list'],
                // ['table' => 'system_user', 'name' => '用户', 'menu_url' => 'admin/system.User/list'],
                // ['table' => 'system_dept', 'name' => '部门', 'menu_url' => 'admin/system.Dept/list'],
                // ['table' => 'system_post', 'name' => '职位', 'menu_url' => 'admin/system.Post/list'],
                // ['table' => 'system_notice', 'name' => '公告', 'menu_url' => 'admin/system.Notice/list'],
            ];

            $user = UserService::info($uid, false);
            foreach ($table as $v) {
                if (in_array($v['menu_url'], $user['roles'] ?? [])) {
                    $where = [];
                    $where = [where_delete()];
                    if ($v['table'] == 'setting_region') {
                        $where[] = ['region_level', '<=', config('admin.region_level', 3)];
                    }

                    $temp = [];
                    $temp['name']  = $v['name'];
                    $temp['count'] = Db::name($v['table'])->where($where)->count();
                    $count[] = $temp;
                }
            }
            $data['count'] = $count;
            Cache::set($key, $data, 3600);
        }

        return $data;
    }
}
