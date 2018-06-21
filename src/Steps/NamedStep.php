<?php

namespace MeadSteve\Tale\Steps;

interface NamedStep extends Step
{
    /**
     * @return string the public name of this step
     */
    public function stepName(): string;
}
