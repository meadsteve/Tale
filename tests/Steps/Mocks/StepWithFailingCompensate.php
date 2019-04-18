<?php

namespace MeadSteve\Tale\Tests\Steps\Mocks;

use MeadSteve\Tale\Steps\NamedStep;

class StepWithFailingCompensate implements NamedStep
{
    public function execute($state)
    {
        return $state;
    }

    /**
     * @param mixed $state
     * @throws \Exception
     */
    public function compensate($state): void
    {
        throw new \Exception("I failed compensating");
    }

    /**
     * @return string the public name of this step
     */
    public function stepName(): string
    {
        return "step with failing compensation";
    }
}
