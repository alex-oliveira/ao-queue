<?php

namespace AoQueue\Workers\Traits;

trait UniqueTrait
{

    /**
     * @var string
     */
    protected $unique = null;

    /**
     * @param string $unique
     * @return $this|null|string
     */
    public function unique($unique = null)
    {
        if (is_null($unique))
            return $this->getUnique();

        return $this->setUnique($unique);
    }

    /**
     * @return null|string
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @param string $unique
     * @return $this
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;

        return $this;
    }

}