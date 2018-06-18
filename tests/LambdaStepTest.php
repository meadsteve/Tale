<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\LambdaStep;
use PHPUnit\Framework\TestCase;

class LambdaStepTest extends TestCase
{
    /**
     * @var LambdaStep
     */
    private $testedStep;

    private $state;

    public function setup()
    {
        $this->state = "not-run";
        $this->testedStep = new LambdaStep(function ($state) {
            $this->state = "run with " . $state;
            return "second-state";
        }, function ($state) {
            $this->state = "compensated with " . $state;
        });
    }

    public function testExecuteReturnsTheResultOfTheWrappedLambda()
    {
        $this->assertEquals(
            "second-state",
            $this->testedStep->execute("whatever")
        );
    }

    public function testExecutePassesTheProvidedStateToTheLambda()
    {
        $this->testedStep->execute("provided-state");
        $this->assertEquals("run with provided-state", $this->state);
    }

    public function testCompensatePassesTheProvidedStateToTheLambda()
    {
        $this->testedStep->compensate("state-to-revert");
        $this->assertEquals("compensated with state-to-revert", $this->state);
    }
}
