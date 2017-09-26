<?php

namespace AoQueue\Console;

use Illuminate\Console\Command;

class RestartCommand extends Command
{

    protected $signature = 'ao-queue:restart';

    protected $description = 'Run the commands "ao-queue:stop --all" and "ao-queue:start" in sequence.';

    public function handle()
    {
        $this->call('ao-queue:stop', ['--all']);
        $this->call('ao-queue:start');
    }

}
