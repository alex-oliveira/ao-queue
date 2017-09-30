<?php

namespace AoQueue\Console;

use AoQueue\Models\Task;
use AoQueue\Workers\TaskWorker;
use Illuminate\Console\Command;

class RunCommand extends Command
{

    protected $signature = 'ao-queue:run {worker_class} {--worker_unique=} {--task_id=} {--param=*}';

    protected $description = 'Run a worker. It also allows you to determine an identification, a task and params.';

    public function handle()
    {
        $worker = app()->make($this->argument('worker_class'));

        $worker->unique(($worker_unique = $this->option('worker_unique')) ?: uniqid())->command($this);

        if (($task_id = $this->option('task_id'))) {
            if (!$worker instanceof TaskWorker)
                throw new \Exception('This Worker isn\'t a TaskWorker.');

            $task = Task::find($task_id);
            if (is_null($task))
                throw new \Exception('Worker Task(' . $task_id . ') not found.');

            $worker->task($task);
        }

        if (($params = $this->params())) {
            $worker->params($params);
        }

        $worker->run();
    }

    private function params()
    {
        $params = $this->option('param');

        foreach ($params as $p => $param) {
            $unserialized = @unserialize($param);
            $params[$p] = $param === 'b:0;' || $unserialized !== false ? $unserialized : $param;
        }

        return count($params) > 0 ? $params : null;
    }

}
