<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

/**
 * 定时任务
 * 调试模式开启：php think timer
 * 守护进程开启：php think timer -m d
 */
class Timer extends Command
{
    /**
     * 定时器
     */
    protected $timer;

    /**
     * 多少秒执行一次
     */
    protected $interval = 5;

    protected function configure()
    {
        // 指令配置
        $this->setName('timer')
            ->addArgument('action', Argument::OPTIONAL, 'start|stop|restart|reload|status|connections', 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, '守护进程方式启动（-m d）')
            ->addOption('interval', 'i', Option::VALUE_OPTIONAL, '多少秒执行一次（-i 5）')
            ->setDescription('定时任务');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('timer:execute');

        $action = $input->getArgument('action');
        $mode = $input->getOption('mode');

        // 重新构造命令行参数,以便兼容workerman的命令
        global $argv;
        $argv = [];
        array_unshift($argv, 'think', $action);
        if ($mode == 'd') {
            $argv[] = '-d';
        } else if ($mode == 'g') {
            $argv[] = '-g';
        }

        $interval = $input->getOption('interval');
        if ($interval) {
            $this->interval = floatval($interval);
        }

        $this->log(implode(' ', $argv));

        Worker::$logFile = runtime_path() . '/log/workerman.log';

        $worker = new Worker();
        $worker->onWorkerStart = [$this, 'start'];
        $worker->runAll();
    }

    public function start()
    {
        $this->timer = \Workerman\Timer::add($this->interval, function () {
            $output = new Output();
            $output->writeln('timer runing ' . date('Y-m-d H:i:s'));

            // 这里执行具体的任务 
            try {
                // 日志清除
                event('LogClear');
            } catch (\Exception $e) {
                $output->writeln('timer ' . $e->getMessage());
                $this->log(['msg' => $e->getMessage()]);
            }
        });
    }

    public function stop()
    {
        \Workerman\Timer::del($this->timer);
    }

    protected function log($data)
    {
        $log['type'] = 'timer';
        $log['data'] = $data;
        trace($log, 'log');
    }
}
