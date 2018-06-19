<?php

namespace MeadSteve\Tale\Steps;

use MeadSteve\Tale\Steps\NamedStep;

class LambdaStep implements NamedStep
{
    /**
     * @var callable
     */
    private $executeHandler;

    /**
     * @var callable
     */
    private $compensateHandler;

    public function __construct(callable $execute, callable $compensate = null)
    {
        $this->executeHandler = $execute;
        if ($compensate === null) {
            $compensate = function () {
            };
        }
        $this->compensateHandler = $compensate;
    }

    public function execute($state)
    {
        $function = $this->executeHandler;
        return $function($state);
    }

    public function compensate($state): void
    {
        $function = $this->compensateHandler;
        $function($state);
    }

    /**
     * @return string the public name of this step
     */
    public function stepName(): string
    {
        return "anonymous lambda";
    }
}
