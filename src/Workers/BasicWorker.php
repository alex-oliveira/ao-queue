<?php

namespace AoQueue\Workers;

use AoQueue\Workers\Traits\CommandTrait;
use AoQueue\Workers\Traits\LogTrait;
use AoQueue\Workers\Traits\ParamsTrait;
use AoQueue\Workers\Traits\UniqueTrait;

abstract class BasicWorker
{

    use CommandTrait, LogTrait, ParamsTrait, UniqueTrait;

    public abstract function work();

    public function run()
    {
        $this->onStart();

        try {
            $this->work();
            $this->onSuccess();
        } catch (\Exception $exception) {
            $this->onError($exception);
        }

        $this->onFinish();
    }

    public function onStart()
    {
        $this->logLevel(0);

        $this->logLine();
        $this->log();
        $this->log('Lets go work!');
        $this->logUp();
        $this->log();
    }

    public function onSuccess()
    {
        $this->logRelevantBox('Work successful. :)');
    }

    public function onError(\Exception $exception)
    {
        $this->logError($exception);
        $this->logRelevant('Work aborted.');
    }

    public function onFinish()
    {
        $this->logDown();
        $this->log('Work finish!');
        $this->log();
        $this->logLine();
    }

}