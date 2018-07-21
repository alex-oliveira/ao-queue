<?php

namespace AoQueue\Workers;

use AoQueue\Constants\Status;
use AoQueue\Models\Task;
use AoQueue\Workers\Traits\TaskTrait;

abstract class TaskWorker extends RepeaterWorker
{

    use TaskTrait;

    public $repeat = false;

    public function next()
    {
        $repeat = parent::next();

        if ($repeat == false && $this->type()->active == 0)
            return false;

        return $this->task || ($this->task = AoQueue()->next($this->type()->id, $this->unique()));
    }

    public function onStart()
    {
        parent::onStart();

        $task = $this->task();
        $task->status = Status::PROCESSING;
        $task->started_at = $task->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        $task->save();
    }

    public function onSuccess()
    {
        $task = $this->task();
        $task->status = Status::FINISHED;
        $task->finished_at = $task->updated_at = $task->deleted_at = \Carbon\Carbon::now()->toDateTimeString();
        $task->save();

        parent::onSuccess();
    }

    public function onError(\Exception $exception)
    {
        $task = $this->task();
        $task->status = Status::ABORTED;
        $task->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        $task->save();

        parent::onError($exception);
    }

    public function onFinish()
    {
        $this->checkFinishWorkGroup();

        Task::query()->where('worker_unique', $this->unique())->update(['worker_unique' => null]);

        $this->task = null;

        parent::onFinish();
    }

    public function checkFinishWorkGroup()
    {
        $task = $this->task();

        if (!$task->group_unique)
            return false;

        $status = [Status::WAITING, Status::SELECTED, Status::PROCESSING];

        $this->log('Checking if exists more tasks to this group...');
        if (Task::query()->where('group_unique', $task->group_unique)->whereIn('status', $status)->exists()) {
            $this->log('This group still has more tasks.');
            $this->log();
            return false;
        }

        $this->log('Issuing end event of group...');
        $this->onFinishWorkGroup();
    }

    public function onFinishWorkGroup()
    {
        $this->logRelevant('Work group work!');
    }

    public function onFinishLoop()
    {
        AoQueue()->start();
        parent::onFinishLoop();
    }

}