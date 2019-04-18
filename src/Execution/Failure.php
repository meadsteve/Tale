<?php

namespace MeadSteve\Tale\Execution;

class Failure implements TransactionResult
{
    /**
     * The reason the transaction failed
     * @var \Throwable
     */
    public $exception;

    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @throws \Throwable
     */
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
