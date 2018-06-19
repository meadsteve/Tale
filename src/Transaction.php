<?php

namespace MeadSteve\Tale;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Transaction
{
    /**
     * @var Step[]
     */
    private $steps = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    public function addStep(Step $step): Transaction
    {
        $this->logger->debug("Adding anonymous step to transaction definition");
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
        $this->logger->debug("Running transaction");
        $state = $startingState;
        $completedSteps = [];
        foreach ($this->steps as $key => $step) {
            try {
                $this->logger->debug("Executing anonymous step [$key]");
                $state = $step->execute($state);
                $completedSteps[] = new CompletedStep($step, $state, $key);
                $this->logger->debug("Execution of anonymous step [$key] complete");
            } catch (\Exception $failure) {
                $this->logger->debug("Failed executing anonymous step [$key]");
                $this->revertCompletedSteps($completedSteps);
                $this->logger->debug("Finished compensating all previous steps");
                return null;
            }
        }
        return $state;
    }

    /**
     * @param CompletedStep[] $completedSteps
     */
    private function revertCompletedSteps(array $completedSteps)
    {
        foreach (array_reverse($completedSteps) as $completedStep) {
            $this->logger->debug("Compensating for step {$completedStep->stepId}");
            $completedStep->step->compensate($completedStep->state);
            $this->logger->debug("Compensation complete for step {$completedStep->stepId}");
        }
    }
}
