<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\job;

use think\queue\Job;

/**
 * 队列任务
 *
 * 执行命令：
 * 本地开发：php think queue:listen --tries=3 --timeout=1800 --memory=1024
 * 正式环境：php think queue:work --tries=3 --timeout=1800 --memory=1024
 * 查看参数：php think queue:listen --help
 * 部分参数说明：
 * --tries   重试次数，必须设置，不然任务会反复执行
 * --timeout 超时时间，耗时间长的任务设置长一些
 * --memory  内存限制，耗内存大的任务设置大一些
 */
class QueueJob
{
    public function fire(Job $job, $data)
    {
        // 这里执行具体的任务 

        // 通过这个方法可以检查这个任务已经重试了几次了
        if ($job->attempts() > 3) {
        }

        // 如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        $job->delete();

        // 也可以重新发布这个任务
        // $job->release(1); //$delay为延迟时间
    }

    public function failed($data)
    {
        // 任务达到最大重试次数后，失败了
        $log['msg']  = '队列任务失败';
        $log['data'] = $data;
        $this->log($log);
    }

    public function log($data)
    {
        $log['type'] = 'queue';
        $log['data'] = $data;
        trace($log, 'log');
    }
}
