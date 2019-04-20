<?php

namespace MeadSteve\Tale\Tests;

use Gamez\Psr\Log\TestLogger;
use MeadSteve\Tale\Exceptions\FailedApplyingAllCompensations;
use MeadSteve\Tale\Execution\Failure;
use MeadSteve\Tale\Steps\LambdaStep;
use MeadSteve\Tale\Tests\State\FakeState;
use MeadSteve\Tale\Tests\Steps\Mocks\FailingStep;
use MeadSteve\Tale\Tests\Steps\Mocks\MockFinalisingStep;
use MeadSteve\Tale\Tests\Steps\Mocks\MockStep;
use MeadSteve\Tale\Tests\Steps\Mocks\StepWithFailingCompensate;
use MeadSteve\Tale\Transaction;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{

    public function testStateIsAutoClonedIfPossible()
    {
        $stateOne = null;
        $stateTwo = null;
        $stepOne = new LambdaStep(
            function ($state) use (&$stateOne) {
                $state->helloFrom = "stateOne";
                $stateOne = $state;
                return $state;
            }
        );
        $stepTwo = new LambdaStep(
            function ($state) use (&$stateTwo) {
                $state->helloFrom = "stateTwo";
                $stateTwo = $state;
                return $state;
            }
        );
        $transaction = (new Transaction())
            ->add($stepOne)
            ->add($stepTwo);

        $startingState = new FakeState();
        $finalState = $transaction->run($startingState)->finalState();
        $finalState->helloFrom = "afterwards";
        $startingState->helloFrom = "beforeButAfter";


        $this->assertNotEquals($startingState, $stateOne);
        $this->assertNotEquals($stateOne, $stateTwo);
        $this->assertNotEquals($stateTwo, $finalState);
    }
}
