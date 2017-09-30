<?php

namespace AoQueue\Workers;

use AoQueue\Workers\Traits\CanTrait;
use AoQueue\Workers\Traits\RepeatTrait;
use AoQueue\Workers\Traits\TypeTrait;

abstract class RepeaterWorker extends BasicWorker
{

    use RepeatTrait, CanTrait, TypeTrait;

    public function __construct()
    {
        $this->type(AoQueue()->type(get_class($this)));
    }

    public function run()
    {
        $this->onStartLoop();

        while ($this->next()) {
            parent::run();
        }

        $this->onFinishLoop();
    }

    public function next()
    {
        $this->onRelax();

        $this->refreshType();

        $this->waitPermissionToWork();

        $this->refreshType();

        $this->logBreak();

        if ($this->type()->active == 0)
            return false;

        return $this->repeat();
    }

    public function onRelax()
    {
        static $first = true;

        if ($first) {
            $first = false;
            return;
        }

        $this->logBreak();
        $this->log();
        $this->log('Checking relax time...');

        if (($s = $this->relaxSeconds()) > 0) {
            $this->log('Yahoo! I have ' . $s . ' second(s) to relax. Let\'s go sleep!!!');
            sleep($s);
        } else {
            $this->log('I don\'t have time to relax. :(');
        }
    }

    public function onStartLoop()
    {
        $this->logTitle('Hi! Let\'s go to ours infinity work now!');
        $this->logBreak();
    }

    public function onFinishLoop()
    {
        $this->logBreak();
        $this->logTitle('Loop finish. Hasta La Vista Baby!');
        $this->logBreak();
    }

}