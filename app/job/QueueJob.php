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
use think\facade\Log;

/**
 * 队列
 */
class QueueJob
{
    public function fire(Job $job, $data)
    {
        // 记录日志
        $this->log($data);

        // 这里执行具体的任务 

        // 通过这个方法可以检查这个任务已经重试了几次了
        if ($job->attempts() > 3) {
        }

        // 如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        $job->delete();

        // 也可以重新发布这个任务
        // $job->release($delay); //$delay为延迟时间
    }

    public function failed($data)
    {
        // 任务达到最大重试次数后，失败了
    }

    public function log($data)
    {
        Log::write($data, 'queue');
    }
}
