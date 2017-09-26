<?php

namespace AoQueue\Workers\Traits;

use AoQueue\Models\Worker;
use Illuminate\Database\Eloquent\Model;

trait TypeTrait
{

    /**
     * @var Worker|Model;
     */
    protected $type = null;

    /**
     * @var int
     */
    protected $type_last_load = 0;

    /**
     * @param Worker $type
     * @return $this|null|Worker|Model
     */
    public function type($type = null)
    {
        if (is_null($type))
            return $this->getType();

        return $this->setType($type);
    }

    /**
     * @return null|Worker
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Worker $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->type_last_load = time();

        return $this;
    }

    /**
     * Return a number list of days at that the worker can work.
     *
     * Ex: "[1, 2]" to monday and thursday.
     *
     * @return integer[]
     */
    public function workDays()
    {
        return explode(',', $this->type->work_days);
    }

    /**
     * Return a integer number that determine the start hour work of worker.
     *
     * Ex: "8" to workers that must work starting at 8 hours of day.
     *
     * @return integer
     */
    public function wakeUpHour()
    {
        return $this->type->wake_up_hour;
    }

    /**
     * Return a integer number that determine the end work hour of worker.
     *
     * Ex: "19" to workers that must stop work at 19 hours of day.
     *
     * @return integer
     */
    public function sleepHour()
    {
        return $this->type->sleep_hour;
    }

    /**
     * Return a quantity of seconds that the worker must wait between tasks.
     *
     * @return integer
     */
    public function relaxSeconds()
    {
        return $this->type->relax_seconds;
    }

}