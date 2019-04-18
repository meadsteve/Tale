<?php

namespace MeadSteve\Tale;

use MeadSteve\Tale\Execution\CompletedStep;
use MeadSteve\Tale\Execution\Failure;
use MeadSteve\Tale\Execution\Success;
use MeadSteve\Tale\Execution\TransactionResult;
use MeadSteve\Tale\Steps\FinalisingStep;
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
        $this->logger = $logger ?? new NullLogger();
    }


    /**
     * Adds a step to the transaction. With the following args:
     *
     *    (Step)                  -> Adds the step to transaction
     *    (Closure, Closure)      -> Creates a step with the first lambda
     *                               as the execute and the second as compensate
     *    (Closure, Closure, str) -> Same as above but named
     *
     * @param mixed ...$args
     * @return Transaction
     */
    public function add(...$args): Transaction
    {
        $step = StepBuilder::build(...$args);
        return $this->addStep($step);
    }

    /**
     * Runs each step in the transaction
     *
     * @param mixed $startingState the state to pass in to the first step
     * @return TransactionResult
     */
    public function run($startingState = []): TransactionResult
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
        $this->finaliseSteps($completedSteps);
        return new Success($state);
    }

    private function addStep(Step $step): Transaction
    {
        $this->logger->debug("Adding {$this->stepName($step)} to transaction definition");
        $this->steps[] = $step;
        return $this;
    }

    /**
     * @param CompletedStep[] $completedSteps
     */
    private function revertCompletedSteps(array $completedSteps): void
    {
        foreach (array_reverse($completedSteps) as $completedStep) {
            $step = $completedStep->step;
            $stepId = $completedStep->stepId;
            $this->logger->debug("Compensating for step {$this->stepName($step)} [{$stepId}]");
            $step->compensate($completedStep->state);
            $this->logger->debug("Compensation complete for step {$this->stepName($step)} [{$stepId}]");
        }
    }

    /**
     * @param CompletedStep[] $completedSteps
     */
    private function finaliseSteps($completedSteps): void
    {
        foreach ($completedSteps as $completedStep) {
            $step = $completedStep->step;
            if ($step instanceof FinalisingStep) {
                $stepId = $completedStep->stepId;
                $this->logger->debug("Finalising step {$this->stepName($step)} [{$stepId}]");
                $step->finalise($completedStep->state);
                $this->logger->debug("Finalising step {$this->stepName($step)} [{$stepId}]");
            }
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
