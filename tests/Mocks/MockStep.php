<?php

namespace MeadSteve\Tale\Tests\Mocks;

use MeadSteve\Tale\NamedStep;

class MockStep implements NamedStep
{
    public $executedState = null;
    public $revertedState = null;

    public function execute($state)
    {
        $this->executedState = $state;
        return $state;
    }

    public function compensate($state): void
    {
        $this->revertedState = $state;
    }

    /**
     * @return string the public name of this step
     */
    public function stepName(): string
    {
        return "mock step";
    }
}
