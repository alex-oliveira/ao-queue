<?php

namespace AoQueue\Workers\Traits;

trait LogTrait
{

    public function logLevel($l = null)
    {
        static $level = 0;

        if (is_null($l))
            return $level == 0 ? '' : str_pad('', $level * 4, ' ', STR_PAD_LEFT) . '│';

        if ($l == 0) {
            $level = 0;
        } elseif (is_numeric($l) && is_int($l + 0)) {
            $level = $level + $l;
        }

        return $level;
    }

    public function logUp()
    {
        $this->logLevel(1);
    }

    public function logDown()
    {
        $this->logLevel(-1);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function logBase($message = '')
    {
        echo "\n" . date('Y-m-d H:i:s') . ' #' . $message;
    }

    public function log($message = '')
    {
        $this->logBase($this->logLevel() . ' ' . $message);
    }

    public function logEmpty($qt = 1)
    {
        for ($c = 1; $c <= $qt; $c++)
            $this->logBase();
    }

    public function logLine()
    {
        $this->logBase('#############################################################################################');
    }

    public function logBox($message = '')
    {
        $level = $this->logLevel();
        $size = (mb_strlen($message) * 3) + 6;

        $this->logBase($level . ' ┌' . str_pad('', $size, '─') . '┐');
        $this->logBase($level . ' │' . ' ' . $message . ' │');
        $this->logBase($level . ' └' . str_pad('', $size, '─') . '┘');
    }

    public function logRelevantBox($message = '')
    {
        $this->log();
        $this->logBox($message);
        $this->log();
    }

    public function logBreak()
    {
        echo "\n\n";
    }

    //------------------------------------------------------------------------------------------------------------------

    public function logTitle($message)
    {
        $this->logLine();
        $this->log($message);
        $this->logLine();
    }

    public function logRelevant($message)
    {
        $this->log();
        $this->log($message);
        $this->log();
    }

    public function logError($exception)
    {
        $this->logBox('ERROR :(');
        $this->logUp();
        $this->log('Class..: ' . get_class($exception), false);
        $this->log('Code...: ' . $exception->getCode(), false);
        $this->log('Message: ' . $exception->getMessage(), false);
        $this->log('Line...: ' . $exception->getLine(), false);
        $this->log('File...: ' . $exception->getFile(), false);
        $this->logDown();
        $this->log();
    }

    public function logFinish($message)
    {
        $this->logLine();
        $this->logSimple();
        $this->logLine();

        echo "\n\n";
    }

}