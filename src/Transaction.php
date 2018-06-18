<?php

namespace MeadSteve\Tale;

class Transaction
{
    /**
     * @var Step[]
     */
    private $steps = [];

    public function addStep(Step $step): Transaction
    {
        $this->steps[] = $step;
        return $this;
    }

    /**
     * Runs each step in the transaction
     *
     * @param mixed $startingState the state to pass in to the first step
     * @return mixed the final state
     */
    public function run($startingState = null)
    {
        $state = $startingState;
        foreach ($this->steps as $step) {
            $state = $step->execute($state);
        }
        return $state;
    }
}
