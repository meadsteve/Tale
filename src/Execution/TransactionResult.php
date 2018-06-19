<?php

namespace MeadSteve\Tale\Execution;

interface TransactionResult
{
    /**
     * @return TransactionResult
     */
    public function throwFailures();

    /**
     * Whatever the final state was after finishing the transaction
     * @return mixed
     */
    public function finalState();
}
