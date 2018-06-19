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
        $completedSteps = [];
        try {
            foreach ($this->steps as $step) {
                $state = $step->execute($state);
                $completedSteps[] = new CompletedStep($step, $state);
            }
        } catch (\Exception $failure) {
            $this->revertCompletedSteps($completedSteps);
            return null;
        }
        return $state;
    }

    /**
     * @param CompletedStep[] $completedSteps
     */
    private function revertCompletedSteps(array $completedSteps)
    {
        foreach (array_reverse($completedSteps) as $completedStep) {
            $completedStep->step->compensate($completedStep->state);
        }
    }
}
