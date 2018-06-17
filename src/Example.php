<?php

namespace ExampleProject;

class Example
{
    protected $parameter;

    /**
     * Example constructor
     *
     * @param $parameter multiplier
     */
    public function __construct($parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Multiple provided value with our multiplier
     *
     * @param $value multiplicand
     * @return integer product of multiplier and multiplicand
     */
    public function multiply($value)
    {
        return $this->parameter * $value;
    }
}
