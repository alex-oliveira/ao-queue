<?php

namespace AoQueue\Workers;

use AoQueue\Constants\Flag;
use AoQueue\Models\Task;
use AoQueue\Workers\Traits\CanTrait;
use AoQueue\Workers\Traits\LogTrait;
use AoQueue\Workers\Traits\TaskTrait;
use AoQueue\Workers\Traits\TypeTrait;
use AoQueue\Workers\Traits\UniqueTrait;
use AoQueue\Workers\Traits\RepeatTrait;
use Illuminate\Console\Command;

abstract class AoQueueWorker
{

    use LogTrait, TypeTrait, UniqueTrait, TaskTrait, CanTrait, RepeatTrait;

    /**
     * @var Command
     */
    protected $command;

    public function __construct()
    {
        $this->type(AoQueue()->worker(get_class($this)));
    }

    public abstract function work();

    //------------------------------------------------------------------------------------------------------------------

    public function bootstrap($command)
    {
        $this->command = $command;

        $this->run();
    }

    private function run()
    {
        $this->onStart();

        while ($this->next()) {
            $this->onStartWork();

            try {
                $this->work();
                $this->onSuccess();
            } catch (\Exception $exception) {
                $this->onError($exception);
            }

            if ($this->checkFinishWorkGroup())
                $this->onFinishWorkGroup();

            $this->detachTask();

            $this->onFinishWork();

            $this->waitRelax();
        }

        $this->onFinish();
    }

    //------------------------------------------------------------------------------------------------------------------

    public function next()
    {
        $this->refresh();

        $this->waitAuthorization();

        if ($this->type()->active == 0)
            return false;

        if ($this->repeat())
            return true;

        return $this->task || ($this->task = AoQueue()->next($this->type()->id, $this->unique()));
    }

    public function refresh()
    {
        if (time() - $this->type_last_load > 5)
            $this->setType($this->getType()->fresh());
    }

    public function waitAuthorization()
    {
        $this->canWork();
        $this->logBreak();
    }

    public function detachTask()
    {
        $this->task = null;
        Task::query()->where('unique', $this->unique())->update(['unique' => null]);
    }

    public function waitRelax()
    {
        $this->log('Checking relax time...');

        if (($s = $this->relaxSeconds()) > 0) {
            $this->log('Yahoo! I have ' . $s . ' second(s) to relax. Let\'s go sleep!!!');
            sleep($s);
        } else {
            $this->log('I don\'t have time to relax. :(');
        }

        $this->log();
        $this->logLine();
        $this->logBreak();
    }

    //------------------------------------------------------------------------------------------------------------------

    public function onStart()
    {
        $this->logTitle('Hello! I am a "' . $this->type()->name . '" and I go work now!');
        $this->logBreak();
    }

    public function onStartWork()
    {
        $this->logLevel(0);

        $this->logLine();
        $this->log();
        $this->log('Lets go work!' . (($task = $this->task()) ? ' Task(' . $task->id . ')' : ''));
        $this->logUp();
        $this->log();

        if (($task = $this->task())) {
            $task->flag_id = Flag::PROCESSING;
            $task->started_at = $task->updated_at = \Carbon\Carbon::now()->toDateTimeString();
            $task->save();
        }
    }

    public function onSuccess()
    {
        if (($task = $this->task())) {
            $task->flag_id = Flag::FINISHED;
            $task->finished_at = $task->updated_at = $task->deleted_at = \Carbon\Carbon::now()->toDateTimeString();
            $task->save();
        }

        $this->logRelevantBox('Work successful. :)');
    }

    public function onError(\Exception $exception)
    {
        if (($task = $this->task())) {
            $task->flag_id = Flag::ABORTED;
            $task->updated_at = \Carbon\Carbon::now()->toDateTimeString();
            $task->save();
        }

        $this->logError($exception);
        $this->logRelevant('Work aborted.');
    }

    public function checkFinishWorkGroup()
    {
        if (!($task = $this->task()))
            return false;

        if (!$task->group_unique)
            return false;

        $flags = [Flag::WAITING, Flag::SELECTED, Flag::PROCESSING];

        $this->log('Checking if exists more tasks to this group...');
        if (Task::query()->where('group_unique', $task->group_unique)->whereIn('flag_id', $flags)->exists()) {
            $this->log('Group still has more tasks.');
            $this->log();
            return false;
        }

        $this->log('Issuing end event of group...');
        return true;
    }

    public function onFinishWorkGroup()
    {
        $this->logRelevant('Work group work!');
    }

    public function onFinishWork()
    {
        $this->logDown();
        $this->log('Work finish!');
        $this->log();
    }

    public function onFinish()
    {
        AoQueue()->start();

        $this->logBreak();
        $this->logTitle('It is the finish of my life. Hasta La Vista Baby!');
        $this->logBreak();
    }

}