<?php

namespace AoQueue\Workers\Traits;

use Illuminate\Console\Command;

trait CommandTrait
{

    /**
     * @var null|Command
     */
    protected $command = null;

    /**
     * @param null|Command $command
     * @return $this|null|Command
     */
    public function command($command = null)
    {
        if (is_null($command))
            return $this->getCommand();
        return $this->setCommand($command);
    }

    /**
     * @return null|Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param Command $command
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

}