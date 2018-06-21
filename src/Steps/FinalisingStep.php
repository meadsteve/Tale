<?php

namespace MeadSteve\Tale\Steps;

interface FinalisingStep extends Step
{
    /**
     * This function is called after all steps in the transaction have been
     * completed.
     *
     * @param mixed $state The state after this step was completed
     * @return void
     */
    public function finalise($state): void;
}
