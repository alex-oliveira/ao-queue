<?php

namespace AoQueue\Workers\Ready;

use AoQueue\Workers\RepeaterWorker;

class SleepTaskFinderWorker extends RepeaterWorker
{

    public function work()
    {
        $tasks = [];

        $qt = rand(5, 25);
        for ($c = 1; $c <= $qt; $c++) {
            $tasks[] = rand(1, 100000000);
        }

        AoQueue()->add(SleeperWorker::class, $tasks, uniqid());

        $this->log(count($tasks) . ' task(s) have been added.');
    }

}