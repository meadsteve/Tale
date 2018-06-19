<?php

namespace MeadSteve\Tale\Steps;

interface Step
{
    /**
     * This function is called when all previous steps have succeeded.
     *
     * @param mixed $state The state passed in from the previous step
     * @return mixed The state after completing this step
     */
    public function execute($state);

    /**
     * This function is called if a state later in the flow has failed. It
     * is expected to reverse whatever was applied in execute
     *
     * @param mixed $state The state that was returned on this classes execute
     */
    public function compensate($state) : void;
}
