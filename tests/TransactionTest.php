<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\LambdaStep;
use MeadSteve\Tale\Tests\Mocks\FailingStep;
use MeadSteve\Tale\Tests\Mocks\MockStep;
use MeadSteve\Tale\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testExecutesStepWithStartingState()
    {
        $mockStep = new MockStep();
        $transaction = (new Transaction())->addStep($mockStep);
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
            ->addStep($stepOne)
            ->addStep($stepTwo);

        $this->assertEquals("zero|one|two", $transaction->run("zero"));
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
            ->addStep($stepOne)
            ->addStep($stepTwo)
            ->addStep(new FailingStep());

        $transaction->run("zero");

        $expectedEvents = [
            'Ran step 1 with: zero',
            'Ran step 2 with: zero|one',
            'Reverted step 2 from: zero|one|two',
            'Reverted step 1 from: zero|one'
        ];
        $this->assertEquals($events, $expectedEvents);
    }
}
