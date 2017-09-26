<?php

namespace AoQueue\Console;

use AoQueue\Models\Task;
use Illuminate\Console\Command;

class RunCommand extends Command
{

    protected $signature = 'ao-queue:run {worker_class} {--unique=} {--task_id=}';

    protected $description = 'Run a worker. It also allows you to determine a task and an identification.';

    public function handle()
    {
        $worker_class = $this->argument('worker_class');

        $worker = app()->make($worker_class);

        $worker->unique(($worker_unique = $this->option('unique')) ?: uniqid());

        if (($task_id = $this->option('task_id'))) {
            $task = Task::find($task_id);
            if (is_null($task))
                throw new \Exception('Worker Task(' . $task_id . ') not found.');

            $worker->task($task);
        }

        $worker->bootstrap($this);
    }

}
