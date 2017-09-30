<?php

namespace AoQueue\Console;

use Cybertron\Robots\Bumblebee;
use Illuminate\Console\Command;

class StopCommand extends Command
{

    protected $signature = 'ao-queue:stop {--pid=} {--unique=} {--type=} {--all}';

    protected $description = 'Destroy workers running in screens.';

    public function handle()
    {
        $screens = AoQueue()->screens();

        if (count($screens) <= 0) {
            echo "\n # ";
            echo "\n # AoQueue Screens not found.";
            echo "\n # \n \n";
            return false;
        }

        if ($this->runWithoutQuestions($screens)) {
            exec('screen -wipe');
            return true;
        }

        echo "\n # ";
        echo "\n # SCREEN LIST";
        echo "\n # ";
        echo "\n # |-------|------------------------|---------------|------------------------------------------------|";
        echo "\n # | PID   | STARTED AT             | UNIQUE        | TYPE                                           |";
        echo "\n # |-------|------------------------|---------------|------------------------------------------------|";
        foreach ($screens as $screen) {
            echo "\n # | ";
            echo str_pad($screen->pid, 5) . ' | ';
            echo str_pad($screen->date . ' às ' . $screen->time, 23) . ' |';
            echo str_pad($screen->unique, 14) . ' | ';
            echo $screen->type_class;
        }
        echo "\n # |-------|------------------------|---------------|------------------------------------------------|";
        echo "\n # \n \n";

        echo "\n # ";
        echo "\n # STOP TYPES";
        echo "\n # ";
        echo "\n # all....: Kill all screens.";
        echo "\n # type...: Kill screen by \"TYPE\" of worker.";
        echo "\n # unique.: Kill screen by \"UNIQUE\" of worker.";
        echo "\n # pid....: Kill screen by \"PID\" number of process in server.";
        echo "\n # \n \n";

        $stops = ['all', 'type', 'pid', 'unique'];
        $stop = $this->askWithCompletion('What is the STOP TYPE?', $stops);

        switch ($stop) {
            case 'pid':
                $this->stopByPid($this->askWithCompletion('What is the PID?', collect($screens)->pluck('pid')->all()));
                break;

            case 'unique':
                $this->stopByAttr($screens, 'unique', $this->askWithCompletion('What is the UNIQUE?', collect($screens)->pluck('unique')->all()));
                break;

            case 'type':
                $this->stopByAttr($screens, 'type', $this->askWithCompletion('What is the TYPE?', collect($screens)->pluck('type')->all()));
                break;

            case 'all':
                $this->stopAll($screens);
                break;
        }

        exec('screen -wipe');
    }

    //------------------------------------------------------------------------------------------------------------------

    public function runWithoutQuestions($screens)
    {
        $pid = $this->option('pid');
        $unique = $this->option('unique');
        $type = $this->option('type');
        $all = $this->option('all');

        if (!is_null($pid)) {
            $this->stopByPid($pid);
            return true;

        } elseif (!is_null($unique)) {
            $this->stopByAttr($screens, 'unique', $unique);
            return true;

        } elseif (!is_null($type)) {
            $this->stopByAttr($screens, 'type', $type);
            return true;

        } elseif ($all) {
            $this->stopAll($screens);
            return true;
        }

        return false;
    }

    //------------------------------------------------------------------------------------------------------------------

    public function kill($pid)
    {
        exec("kill -9 $pid");
    }

    public function stopByPid($pid)
    {
        $this->kill($pid);

        echo "\n # ";
        echo "\n # The screen with PID \"$pid\" is finished.";
        echo "\n # \n \n";
    }

    public function stopByAttr($screens, $attr, $value)
    {
        foreach ($screens as $screen)
            if ($screen->{$attr} == $value)
                $this->kill($screen->pid);

        echo "\n # ";
        echo "\n # All screen with " . mb_strtoupper($attr) . " \"$value\" are finished.";
        echo "\n # \n \n";
    }

    public function stopAll($screens)
    {
        foreach ($screens as $screen)
            $this->kill($screen->pid);

        echo "\n # ";
        echo "\n # All screen are finished.";
        echo "\n # \n \n";
    }

}