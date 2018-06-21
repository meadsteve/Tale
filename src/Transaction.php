<?php

namespace MeadSteve\Tale;

use MeadSteve\Tale\Execution\CompletedStep;
use MeadSteve\Tale\Execution\Failure;
use MeadSteve\Tale\Execution\Success;
use MeadSteve\Tale\Execution\TransactionResult;
use MeadSteve\Tale\Steps\NamedStep;
use MeadSteve\Tale\Steps\Step;
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
        $this->logger->debug("Adding {$this->stepName($step)} to transaction definition");
        $this->steps[] = $step;
        return $this;
    }

    /**
     * Runs each step in the transaction
     *
     * @param mixed $startingState the state to pass in to the first step
     * @return TransactionResult
     */
    public function run($startingState = null): TransactionResult
    {
        $this->logger->debug("Running transaction");
        $state = $startingState;
        $completedSteps = [];
        foreach ($this->steps as $key => $step) {
            try {
                $this->logger->debug("Executing {$this->stepName($step)} step [$key]");
                $state = $step->execute($state);
                $completedSteps[] = new CompletedStep($step, $state, $key);
                $this->logger->debug("Execution of {$this->stepName($step)} step [$key] complete");
            } catch (\Throwable $failure) {
                $this->logger->debug("Failed executing {$this->stepName($step)} step [$key]");
                $this->revertCompletedSteps($completedSteps);
                $this->logger->debug("Finished compensating all previous steps");
                return new Failure($failure);
            }
        }
        return new Success($state);
    }

    /**
     * @param CompletedStep[] $completedSteps
     */
    private function revertCompletedSteps(array $completedSteps)
    {
        foreach (array_reverse($completedSteps) as $completedStep) {
            $step = $completedStep->step;
            $stepId = $completedStep->stepId;
            $this->logger->debug("Compensating for step {$this->stepName($step)} [{$stepId}]");
            $step->compensate($completedStep->state);
            $this->logger->debug("Compensation complete for step {$this->stepName($step)} [{$stepId}]");
        }
    }

    private function stepName(Step $step): string
    {
        if ($step instanceof NamedStep) {
            return "`{$step->stepName()}`";
        }
        return "anonymous step";
    }
}
