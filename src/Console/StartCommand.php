<?php

namespace AoQueue\Console;

use AoQueue\Workers\Ready\MasterWorker;
use Illuminate\Console\Command;

class StartCommand extends Command
{

    protected $signature = 'ao-queue:start {--no-repeat}';

    protected $description = 'Start the MasterWorker.';

    public function handle()
    {
        $this->call('ao-queue:screen', ['worker_class' => MasterWorker::class, '--param' => [$this->option('no-repeat')]]);
    }

}
