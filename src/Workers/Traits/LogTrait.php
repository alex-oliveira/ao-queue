<?php

namespace AoQueue\Workers\Traits;

trait LogTrait
{

    protected $level = 0;

    public function logUp()
    {
        $this->level++;
        $this->log('');
    }

    public function logDown()
    {
        $this->log('');
        $this->level--;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function log($message = '')
    {
        $ident = $this->level == 0 ? '' : str_pad(' ', $this->level * 4, ' ', STR_PAD_LEFT) . '|';
        echo "\n# " . date('Y-m-d H:i:s') . ' # ' . $ident . ' ' . $message;
    }

    public function logSimple($message = '')
    {
        echo "\n# " . $message;
    }

    public function logLine()
    {
        echo "\n######################################################################################################";
    }

    public function logEmpty($qt = 1)
    {
        for ($c = 1; $c <= $qt; $c++)
            echo "\n";
    }

    //------------------------------------------------------------------------------------------------------------------

    public function logStart()
    {
        $this->logLine();
        $this->logSimple('Hello! I am a "' . $this->type()->name . '" and I go work now!');
        $this->logLine();

        $this->logEmpty(2);
    }

    public function logStartWork()
    {
        $this->logLine();
        $this->log();
        $this->log('Lets go work!' . (($task = $this->task()) ? ' Task(' . $task->id . ')' : ''));
        $this->logUp();
    }

    public function logSuccess()
    {
        $this->log();
        $this->log('Work successful. :)');
    }

    public function logError($exception)
    {
        $this->log();
        $this->log('ERROR :(');

        $this->logUp();
        $this->log('Class..: ' . get_class($exception), false);
        $this->log('Code...: ' . $exception->getCode(), false);
        $this->log('Message: ' . $exception->getMessage(), false);
        $this->log('Line...: ' . $exception->getLine(), false);
        $this->log('File...: ' . $exception->getFile(), false);
        $this->logDown();

        $this->log();
        $this->log('Work aborted.');
    }

    public function logFinishWork()
    {
        $this->logDown();
        $this->log('Work finish!');
        $this->log();
    }

    public function logFinishWorkGroup()
    {
        $this->log('Work group work!');
        $this->log();
    }

    public function logFinish()
    {
        $this->logLine();
        $this->logSimple('It is the finish of my life. Hasta La Vista Baby!');
        $this->logLine();

        $this->logEmpty(2);
    }

}