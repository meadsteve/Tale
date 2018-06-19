<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\Steps\LambdaStep;
use MeadSteve\Tale\Tests\Steps\Mocks\FailingStep;
use MeadSteve\Tale\Tests\Steps\Mocks\MockStep;
use MeadSteve\Tale\Transaction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TransactionTest extends TestCase
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function setUp()
    {
        $this->logger = new \Monolog\Logger("log");
        $this->logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());
    }

    public function testExecutesStepWithStartingState()
    {
        $mockStep = new MockStep();
        $transaction = (new Transaction($this->logger))->addStep($mockStep);
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
        $transaction = (new Transaction($this->logger))
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

        $transaction = (new Transaction($this->logger))
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
