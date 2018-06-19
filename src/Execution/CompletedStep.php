<?php

namespace MeadSteve\Tale\Execution;

use MeadSteve\Tale\Steps\Step;

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
     * @var string
     */
    public $stepId;

    /**
     * @param Step $step
     * @param mixed $state the state after running the state above
     * @param string $stepId
     */
    public function __construct(Step $step, $state, $stepId)
    {
        $this->step = $step;
        $this->state = $state;
        $this->stepId = $stepId;
    }
}
