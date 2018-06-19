<?php

namespace MeadSteve\Tale\Tests\Steps\Mocks;

use MeadSteve\Tale\Steps\NamedStep;

class FailingStep implements NamedStep
{
    public function execute($state)
    {
        throw new \Exception("I always fail");
        return $state;
    }

    public function compensate($state): void
    {
        // Not Needed
    }

    /**
     * @return string the public name of this step
     */
    public function stepName(): string
    {
        return "step that will fail";
    }
}
