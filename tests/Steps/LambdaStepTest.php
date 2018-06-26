<?php

namespace MeadSteve\Tale\Tests\Steps;

use MeadSteve\Tale\Steps\LambdaStep;
use PHPUnit\Framework\TestCase;

class LambdaStepTest extends TestCase
{
    /**
     * @var LambdaStep
     */
    private $testedStep;

    private $state;

    public function setupLamda($name = null)
    {
        $this->state = "not-run";
        $this->testedStep = new LambdaStep(function ($state) {
            $this->state = "run with " . $state;
            return "second-state";
        }, function ($state) {
            $this->state = "compensated with " . $state;
        }, $name);
    }

    public function testExecuteReturnsTheResultOfTheWrappedLambda()
    {
        $this->setupLamda();
        $this->assertEquals(
            "second-state",
            $this->testedStep->execute("whatever")
        );
    }

    public function testExecutePassesTheProvidedStateToTheLambda()
    {
        $this->setupLamda();
        $this->testedStep->execute("provided-state");
        $this->assertEquals("run with provided-state", $this->state);
    }

    public function testCompensatePassesTheProvidedStateToTheLambda()
    {
        $this->setupLamda();
        $this->testedStep->compensate("state-to-revert");
        $this->assertEquals("compensated with state-to-revert", $this->state);
    }
    public function testLambdaStepsCanBeNamed()
    {
        $this->setupLamda("my name");
        $this->assertEquals("my name", $this->testedStep->stepName());
    }
}
