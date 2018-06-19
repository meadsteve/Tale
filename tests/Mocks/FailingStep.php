<?php

namespace MeadSteve\Tale\Tests\Mocks;

use MeadSteve\Tale\Step;

class FailingStep implements Step
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
}
