<?php

namespace AoQueue\Workers\Traits;

use AoQueue\Models\Type;
use Illuminate\Database\Eloquent\Model;

trait TypeTrait
{

    /**
     * @var Type|Model;
     */
    protected $type = null;

    /**
     * @var int
     */
    protected $last_set = 0;

    /**
     * @param Type $type
     * @return $this|null|Type|Model
     */
    public function type($type = null)
    {
        if (is_null($type))
            return $this->getType();
        return $this->setType($type);
    }

    /**
     * @return null|Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->last_set = time();
        return $this;
    }

    public function refreshType()
    {
        if (time() - $this->last_set > 2)
            $this->type($this->type->fresh());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Return a number list of days at that the worker can work.
     *
     * Ex: "[1, 2]" to monday and thursday.
     *
     * @return integer[]
     */
    public function workDays()
    {
        return $this->type->work_days;
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
    public function lockSeconds()
    {
        return $this->type->lock_seconds;
    }

}