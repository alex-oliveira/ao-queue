<?php

namespace AoQueue\Workers\Traits;

trait ParamsTrait
{

    /**
     * @var mixed
     */
    protected $params = null;

    /**
     * @param mixed $params
     * @return $this|mixed
     */
    public function params($params = null)
    {
        if (is_null($params))
            return $this->getParams();
        return $this->setParams($params);
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

}