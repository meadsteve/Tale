<?php

namespace MeadSteve\Tale\Execution;

use MeadSteve\Tale\State\CloneableState;

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
        // Nothing to be thrown as this is a success
        return $this;
    }

    public function finalState()
    {
        if ($this->finalState instanceof CloneableState) {
            return $this->finalState->cloneState();
        }
        return $this->finalState;
    }
}
