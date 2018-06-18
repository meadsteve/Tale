<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\LambdaStep;
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
}
