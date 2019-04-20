<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\Exceptions\FailedApplyingAllCompensations;
use MeadSteve\Tale\Execution\Failure;
use MeadSteve\Tale\Steps\LambdaStep;
use MeadSteve\Tale\Tests\Steps\Mocks\FailingStep;
use MeadSteve\Tale\Tests\Steps\Mocks\MockStep;
use MeadSteve\Tale\Tests\Steps\Mocks\StepWithFailingCompensate;
use MeadSteve\Tale\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testExecutesStepWithStartingState()
    {
        $mockStep = new MockStep();
        $transaction = (new Transaction())->add($mockStep);
        $transaction->run("starting_state");

        $this->assertEquals("starting_state", $mockStep->executedState);
    }

    public function testExecutesEachStepInTurn()
    {
        $stepOne = new LambdaStep(
            function ($state) {
                return $state . "|one";
            }
        );
        $stepTwo = new LambdaStep(
            function ($state) {
                return $state . "|two";
            }
        );
        $transaction = (new Transaction())
            ->add($stepOne)
            ->add($stepTwo);

        $this->assertEquals("zero|one|two", $transaction->run("zero")->finalState());
    }

    public function testAfterAFailedStepEachPreviousStepIsReverted()
    {
        $events = [];
        $stepOne = new LambdaStep(
            function ($state) use (&$events) {
                $events[] = "Ran step 1 with: " . $state;
                return "$state|one";
            },
            function ($stateToRevert) use (&$events) {
                $events[] = "Reverted step 1 from: " . $stateToRevert;
            }
        );

        $stepTwo = new LambdaStep(
            function ($state) use (&$events) {
                $events[] = "Ran step 2 with: " . $state;
                return "$state|two";
            },
            function ($stateToRevert) use (&$events) {
                $events[] = "Reverted step 2 from: " . $stateToRevert;
            }
        );

        $transaction = (new Transaction())
            ->add($stepOne)
            ->add($stepTwo)
            ->add(new FailingStep());

        $transaction->run("zero");

        $expectedEvents = [
            'Ran step 1 with: zero',
            'Ran step 2 with: zero|one',
            'Reverted step 2 from: zero|one|two',
            'Reverted step 1 from: zero|one'
        ];
        $this->assertEquals($events, $expectedEvents);
    }

    public function testErrorsAreCaughtAsWellAsExceptions()
    {
        $failureStep = new LambdaStep(
            function ($state) {
                throw new \Error("I'm a little error. Short and bad.");
            },
            function ($stateToRevert) {
            }
        );

        $transaction = (new Transaction())
            ->add($failureStep);

        $result = $transaction->run();

        $this->assertInstanceOf(\Error::class, $result->finalState());
    }

    public function testAFailObjectWithTheFailingExceptionIsReturned()
    {

        $transaction = (new Transaction())
            ->add(new FailingStep());

        $result = $transaction->run("zero");

        $this->assertInstanceOf(Failure::class, $result);
        $this->assertInstanceOf(\Exception::class, $result->exception);
    }

    public function testTransactionFailuresCanBeRethrown()
    {

        $transaction = (new Transaction())
            ->add(new FailingStep());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("I always fail");

        $transaction
            ->run("zero")
            ->throwFailures();
    }

    public function testThrowingASuccessDoesNothingButPassTheResultThrough()
    {

        $transaction = (new Transaction())
            ->add(new MockStep());

        $result = $transaction
            ->run("expected_result")
            ->throwFailures()
            ->finalState();

        $this->assertEquals("expected_result", $result);
    }

    public function testFailuresInCompensationAreCaught()
    {

        $firstStep = new MockStep();
        $transaction = (new Transaction())
            ->add($firstStep)
            ->add(new StepWithFailingCompensate())
            ->add(new FailingStep());

        $this->expectException(FailedApplyingAllCompensations::class);
        $this->expectExceptionMessage("Failed applying all compensation steps");

        try {
            $transaction->run("some payload");
        } finally {
            # The reverted state should not be null as it should have
            # been compensated
            $this->assertNotNull($firstStep->revertedState);
        }
    }
}
