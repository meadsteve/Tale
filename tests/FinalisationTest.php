<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\Tests\Steps\Mocks\FailingStep;
use MeadSteve\Tale\Tests\Steps\Mocks\MockFinalisingStep;
use MeadSteve\Tale\Transaction;
use PHPUnit\Framework\TestCase;

class FinalisationTest extends TestCase
{
    public function testFinaliseMethodsAreCalledIfTheTransactionIsASuccess()
    {

        $mockStep = new MockFinalisingStep();
        $transaction = (new Transaction())
            ->add($mockStep);

        $transaction
            ->run("expected_result")
            ->finalState();

        $this->assertEquals("expected_result", $mockStep->finalisedState);
    }

    public function testFinaliseMethodsArentCalledIfTheTransactionFails()
    {

        $mockStep = new MockFinalisingStep();
        $transaction = (new Transaction())
            ->add($mockStep)
            ->add(new FailingStep());

        $transaction
            ->run("expected_result")
            ->finalState();

        $this->assertNull($mockStep->finalisedState);
    }
}
