<?php

namespace AoQueue\Console;

use Illuminate\Console\Command;

class StartCommand extends Command
{

    protected $signature = 'ao-queue:start';

    protected $description = 'Cria e coloca para trabalhar um "Bumblebee/Finder", responsável por manter os outros "Finders" vivos.';

    public function handle()
    {
        $this->call('ao-queue:screen', ['worker_class' => \AoQueue\Workers\MasterWorker::class]);
    }

}
