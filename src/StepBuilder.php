<?php

namespace MeadSteve\Tale;

use MeadSteve\Tale\Exceptions\FailureToBuildStep;
use MeadSteve\Tale\Steps\LambdaStep;
use MeadSteve\Tale\Steps\Step;

class StepBuilder
{

    /**
     * @param mixed[] ...$args
     * @return Step
     */
    public static function build(...$args): Step
    {
        if (sizeof($args) == 1 && $args[0] instanceof Step) {
            return $args[0];
        }
        if (sizeof($args) == 3
            && is_string($args[2])
            && $args[0] instanceof \Closure
            && $args[1] instanceof \Closure
        ) {
            return new LambdaStep($args[0], $args[1], $args[2]);
        }
        if (sizeof($args) == 2
            && $args[0] instanceof \Closure
            && $args[1] instanceof \Closure
        ) {
            return new LambdaStep($args[0], $args[1]);
        }
        throw new FailureToBuildStep("Not sure how to build a step from provided data");
    }
}
