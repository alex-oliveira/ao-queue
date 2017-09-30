<?php

namespace AoQueue\Console;

use Cybertron\Models\Robot;
use Cybertron\Robots\Cybertron;
use Illuminate\Console\Command;

class ScreenCommand extends Command
{

    protected $signature = 'ao-queue:screen {worker_class} {--worker_unique=} {--task_id=} {--param=*}';

    protected $description = 'Run the command "ao-queue:run" in a screen.';

    public function handle()
    {
        $type = AoQueue()->type($this->argument('worker_class'));
        $worker_unique = ($worker_unique = $this->option('worker_unique')) ?: uniqid();

        $command = str_slug(kebab_case($type->name));
        $command = date('Y-m-d.H-i-s') . '.ao-queue.' . $worker_unique . '.' . $type->id . '.' . $command;
        $command = ' screen -dmSL "' . $command . '" ';
        $command .= ' php artisan ao-queue:run "' . $type->class . '" --worker_unique="' . $worker_unique . '" ';

        if (($task_id = $this->option('task_id')))
            $command .= ' --task_id="' . $task_id . '" ';

        foreach ($this->option('param') as $param)
            $command .= ' --param="' . $param . '" ';

        exec($command);
    }

}
