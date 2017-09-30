<?php

namespace AoQueue\Workers\Traits;

trait RepeatTrait
{

    /**
     * The worker repeat your work while this attribute is "true".
     *
     * @var bool
     */
    protected $repeat = true;

    /**
     * @param null|bool $repeat
     * @return $this|bool
     */
    public function repeat($repeat = null)
    {
        if (is_bool($repeat))
            return $this->setRepeat($repeat);
        return $this->getRepeat();
    }

    /**
     * @return bool
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param bool $repeat
     * @return $this
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;
        return $this;
    }

}