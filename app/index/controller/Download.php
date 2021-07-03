<?php
/*
 * @Description  : 下载
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-19
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\DownloadValidate;
use app\common\service\DownloadService;
use app\common\service\DownloadCategoryService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("下载")
 * @Apidoc\Sort("66")
 * @Apidoc\Group("indexCms")
 */
class Download
{
    /**
     * @Apidoc\Title("分类列表")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\DownloadCategoryModel\list")
     *      )
     * )
     */
    public function category()
    {
        $data = [];
        $list = DownloadCategoryService::list('list');
        foreach ($list as $k => $v) {
            if ($v['is_hide'] == 0) {
                $data[] = $v;
            }
        }
        $data = DownloadCategoryService::toTree($data, 0);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载列表")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\indexList")
     * @Apidoc\Returned(ref="returnCode"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page                 = Request::param('page/d', 1);
        $limit                = Request::param('limit/d', 10);
        $sort_field           = Request::param('sort_field/s ', '');
        $sort_type            = Request::param('sort_type/s', '');
        $name                 = Request::param('name/s', '');
        $download_category_id = Request::param('download_category_id/d', '');

        $where[] = ['is_hide', '=', 0];
        $where[] = ['is_delete', '=', 0];
        if ($name) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if ($download_category_id) {
            $where[] = ['download_category_id', '=', $download_category_id];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = DownloadService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("下载信息")
     * @Apidoc\Param(ref="app\common\model\DownloadModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\DownloadModel\info"),
     *      @Apidoc\Returned("prev_info", type="object", desc="上一条",
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\name")
     *      ),
     *      @Apidoc\Returned("next_info", type="object", desc="下一条",
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\id"),
     *          @Apidoc\Returned(ref="app\common\model\DownloadModel\name")
     *      )
     * )
     */
    public function info()
    {
        $param['download_id'] = Request::param('download_id/d', '');

        validate(DownloadValidate::class)->scene('info')->check($param);

        $data = DownloadService::info($param['download_id']);

        if ($data['is_delete'] == 1) {
            exception('下载已被删除');
        }

        if (empty($data['title'])) {
            $data['title'] = $data['name'];
        }

        $data['prev_info'] = DownloadService::prev($data['download_id']);
        $data['next_info'] = DownloadService::next($data['download_id']);

        return success($data);
    }
}
