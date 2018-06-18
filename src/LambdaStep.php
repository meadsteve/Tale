<?php

namespace MeadSteve\Tale;

class LambdaStep implements Step
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
}
