<?php

namespace AoQueue\Workers;

class SleeperWorker extends AoQueueWorker
{

    public function work()
    {
        $this->log('I am a specialist worker in sleep.');
        $this->log('Then, I go sleep ' . ($s = rand(2, 10)) . ' seconds.');

        sleep($s);
    }

}