<?php

namespace MeadSteve\Tale\Steps;

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

    /**
     * @var string
     */
    private $name;

    public function __construct(callable $execute, callable $compensate = null, string $name = null)
    {
        $this->executeHandler = $execute;
        $this->compensateHandler = $compensate ?? function () {
        };

        $this->name = $name ?? "anonymous lambda";
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
        return $this->name;
    }
}
