<?php

namespace MeadSteve\Tale;

class CompletedStep
{
    /**
     * @var Step
     */
    public $step;

    /**
     * @var mixed
     */
    public $state;

    /**
     * @param Step $step
     * @param mixed $state the state after running the state above
     */
    public function __construct(Step $step, $state)
    {
        $this->step = $step;
        $this->state = $state;
    }
}
