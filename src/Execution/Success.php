<?php

namespace MeadSteve\Tale\Execution;

class Success implements TransactionResult
{
    /**
     * @var mixed whatever the state of running the transaction was
     */
    private $finalState;

    /**
     * @param mixed $finalState whatever the state of running the transaction was
     */
    public function __construct($finalState)
    {
        $this->finalState = $finalState;
    }

    public function throwFailures()
    {
        // noop - A success isn't a failure
    }

    public function finalState()
    {
        return $this->finalState;
    }
}
