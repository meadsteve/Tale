<?php

namespace MeadSteve\Tale\Tests\Mocks;

use MeadSteve\Tale\Step;

class MockStep implements Step
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
}
