<?php

namespace AoQueue\Workers\Traits;

trait RepeatTrait
{

    /**
     * The worker repeat your work while this attribute is "true".
     *
     * @var bool
     */
    protected $repeat = false;

    /**
     * @param null|bool $repeat
     * @return $this|bool
     */
    public function repeat($repeat = null)
    {
        if (is_null($repeat))
            return $this->getRepeat();
        return $this->setRepeat($repeat);
    }

    /**
     * @return bool
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param $repeat
     * @return $this
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;
        return $this;
    }

}