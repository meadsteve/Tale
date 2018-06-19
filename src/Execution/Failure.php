<?php

namespace MeadSteve\Tale\Execution;

class Failure implements TransactionResult
{
    /**
     * The reason the transaction failed
     * @var \Exception
     */
    public $exception;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function throwFailures()
    {
        throw $this->exception;
    }

    /**
     * Whatever the final state was after finishing the transaction
     * @return mixed
     */
    public function finalState()
    {
        return $this->exception;
    }
}
