<?php

namespace MeadSteve\Tale\Execution;

interface TransactionResult
{
    /**
     * @return void
     */
    public function throwFailures();

    /**
     * Whatever the final state was after finishing the transaction
     * @return mixed
     */
    public function finalState();
}
