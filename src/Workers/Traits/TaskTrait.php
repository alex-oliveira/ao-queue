<?php

namespace AoQueue\Workers\Traits;

use AoQueue\Models\Task;

trait TaskTrait
{

    /**
     * @var null|Task
     */
    protected $task = null;

    /**
     * @param Task $task
     * @return $this|null|Task
     */
    public function task($task = null)
    {
        if (is_null($task))
            return $this->getTask();
        return $this->setTask($task);
    }

    /**
     * @return null|Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function setTask($task)
    {
        $this->task = $task;
        return $this;
    }

}