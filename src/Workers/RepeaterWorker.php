<?php

namespace AoQueue\Workers;

use AoQueue\Workers\Traits\CanTrait;
use AoQueue\Workers\Traits\RepeatTrait;
use AoQueue\Workers\Traits\TypeTrait;
use Carbon\Carbon;

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
        $this->onLock();

        $this->refreshType();

        $this->waitPermissionToWork();

        $this->refreshType();

        $this->logBreak();

        if ($this->type()->active == 0)
            return false;

        return $this->repeat();
    }

    public function onLock()
    {
        static $first = true;

        if ($first) {
            $first = false;
            return;
        }

        $this->log();
        $this->log('Checking lock time...');

        if (($s = $this->lockSeconds()) > 0) {
            $this->log('Yahoo! I have ' . $s . ' second(s) to stay locked. Let\'s go sleep!!!');
            sleep($s);
        } else {
            $this->log('I don\'t have time to stay locked. :(');
        }
    }

    public function onStartLoop()
    {
        $this->logTitle('Hi! Let\'s go to ours infinity work now!');
        $this->logBreak();
    }

    public function onFinishLoop()
    {
        $this->logTitle('Loop finish. Hasta La Vista Baby!');
        $this->logBreak();
    }

    public function onSuccess()
    {
        $type = $this->type();

        $this->refreshType(1);

        if ($type->ignore_seconds > 0) {

            $next = Carbon::now()->addSeconds($type->ignore_seconds);

            if (empty($type->selectable_at) || (new Carbon($type->selectable_at) < $next)) {
                $type->selectable_at = $next;
            }

        }

        $type->finished_at = Carbon::now();
        $type->save();

        parent::onSuccess();
    }

}