<?php

namespace AoQueue\Workers\Ready;

use AoQueue\Workers\TaskWorker;

class SleeperWorker extends TaskWorker
{

    public function work()
    {
        $this->log('I am a specialist worker in sleep.');
        $this->log('Then, I go sleep ' . ($s = rand(2, 10)) . ' seconds.');

        sleep($s);
    }

}