<?php

namespace AoQueue\Console;

use Cybertron\Models\Robot;
use Cybertron\Robots\Cybertron;
use Illuminate\Console\Command;

class ScreenCommand extends Command
{

    protected $signature = 'ao-queue:screen {worker_class} {--unique=} {--task_id=}';

    protected $description = 'Run the command "ao-queue:run" in a screen.';

    public function handle()
    {
        $worker_type = AoQueue()->worker($this->argument('worker_class'));

        $worker_unique = ($worker_unique = $this->option('unique')) ?: uniqid();

        $command = str_slug(kebab_case($worker_type->name));
        $command = date('Y-m-d.H-i-s') . '.ao-queue.' . $worker_unique . '.' . $worker_type->id . '.' . $command;
        $command = ' screen -dmSL "' . $command . '"';
        $command .= ' php artisan ao-queue:run "' . $worker_type->class . '" --unique="' . $worker_unique . '" ';

        if (($task_id = $this->option('task_id')))
            $command .= ' --task_id="' . $task_id . '" ';

        exec($command);
    }

}
