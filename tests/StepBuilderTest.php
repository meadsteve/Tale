<?php

namespace MeadSteve\Tale\Tests;

use MeadSteve\Tale\Exceptions\FailureToBuildStep;
use MeadSteve\Tale\StepBuilder;
use MeadSteve\Tale\Steps\LambdaStep;
use MeadSteve\Tale\Steps\NamedStep;
use MeadSteve\Tale\Tests\Steps\Mocks\MockStep;
use PHPUnit\Framework\TestCase;

class StepBuilderTest extends TestCase
{
    public function testPassesThroughAStepUnchanged()
    {
        $mockStep = new MockStep();
        $outputStep = StepBuilder::build($mockStep);
        $this->assertSame($outputStep, $mockStep);
    }

    public function testTurnsTwoCallablesIntoALambdaStep()
    {
        $functionOne = function () {
            return "function_one";
        };
        $functionTwo = function () {
            return "function_two";
        };

        $outputStep = StepBuilder::build($functionOne, $functionTwo);

        $this->assertInstanceOf(LambdaStep::class, $outputStep);
    }

    public function testTurnsAStringAndTwoCallablesIntoANamedLambdaStep()
    {
        $functionOne = function () {
            return "function_one";
        };
        $functionTwo = function () {
            return "function_two";
        };

        /** @var NamedStep $outputStep */
        $outputStep = StepBuilder::build($functionOne, $functionTwo, "my_function");

        $this->assertInstanceOf(LambdaStep::class, $outputStep);
        $this->assertEquals("my_function", $outputStep->stepName());
    }

    public function testAnExceptionIsThrownIfItsNotPossibleToBuildAStep()
    {
        $this->expectExceptionMessage('Not sure how to build a step from provided data');
        $this->expectException(FailureToBuildStep::class);

        StepBuilder::build("Hugin", "Munin", []);
    }
}
